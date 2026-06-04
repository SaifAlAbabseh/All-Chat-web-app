const WebSocket = require('ws');
const mysql = require('mysql2/promise');
const http = require('http');
const url = require('url');
const path = require('path');
const fs = require('fs');
require('dotenv').config({ path: path.join(__dirname, '..', '.env') });

const server = http.createServer((req, res) => {
    // Return a blank 200 OK response for Render's health checks
    res.writeHead(200, { 'Content-Type': 'text/plain' });
    res.end('');
});

const wss = new WebSocket.Server({ server });

let dbPool;

const hostName = process.env.DB_HOST_NAME;
const username = process.env.DB_USERNAME;
const password = process.env.DB_PASSWORD;
const dbName = process.env.DB_NAME;

try {
    dbPool = mysql.createPool({
        host: hostName,
        user: username,
        password: password,
        database: dbName,
        waitForConnections: true,
        connectionLimit: 10,
        queueLimit: 0
    });
    console.log("Database pool created.");
} catch (err) {
    console.error("Database connection failed:", err);
}

// Map of username -> Set of WebSocket clients
const clients = new Map();

// Helper to decode message like formatMessage in PHP
function formatMessageNode(msg) {
    try {
        // PHP's urldecode equivalent
        let decoded = decodeURIComponent((msg + '').replace(/\+/g, '%20'));

        // PHP's htmlspecialchars(..., ENT_QUOTES) equivalent
        const escapeHtml = (text) => {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function (m) { return map[m]; });
        };
        decoded = escapeHtml(decoded);

        // Format files: [FILE:path:name]
        decoded = decoded.replace(/\[FILE:\s*(.+?):\s*(.+?)\]/g, (match, filepath, name) => {
            const filename = path.basename(filepath);
            const extMatch = filename.match(/\.([^\.]+)$/);
            const ext = extMatch ? extMatch[1].toLowerCase() : '';
            const basePath = process.env.PUBLIC_SITE_URL ? `/${process.env.PUBLIC_SITE_URL}` : '';
            const fullPath = `${basePath}/serveFile.php?f=${encodeURIComponent(filename)}&n=${encodeURIComponent(name)}`;

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                return `<br/><a href='${fullPath}' target='_blank'><img src='${fullPath}' onload='var b=document.getElementById("messages"); if(b) b.scrollTop=b.scrollHeight;' style='max-width:100%; height:auto; max-height:200px; border-radius:10px; margin-top:5px; display:block; box-sizing:border-box;' alt='${name}'/></a>`;
            } else if (['mp4', 'webm'].includes(ext)) {
                return `<br/><video src='${fullPath}' onloadeddata='var b=document.getElementById("messages"); if(b) b.scrollTop=b.scrollHeight;' controls style='max-width:100%; height:auto; max-height:200px; border-radius:10px; margin-top:5px; display:block; box-sizing:border-box;'></video>`;
            } else if (['mp3', 'wav'].includes(ext)) {
                return `<br/><audio src='${fullPath}' controls style='max-width:100%; margin-top:5px; display:block; box-sizing:border-box;'></audio>`;
            } else {
                return `<br/><a href='${fullPath}' target='_blank' class='chatFileLink' style='display:inline-block; margin-top:5px; background:rgba(255,255,255,0.2); padding:5px 10px; border-radius:10px; text-decoration:none; color:inherit;'>📎 ${name}</a>`;
            }
        });

        // PHP's preg_replace for links
        const pattern = /(https?:\/\/[^\s]+)/g;
        const replacement = '<a href="$1" target="_blank" rel="noopener noreferrer" class="chatMessageLink">$1</a>';
        decoded = decoded.replace(pattern, replacement);

        // PHP's nl2br
        decoded = decoded.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');

        return decoded;
    } catch (e) {
        return msg;
    }
}

function deleteFilesFromMessage(messageEncoded) {
    if (!messageEncoded) return;
    try {
        const decoded = decodeURIComponent(messageEncoded);
        const fileRegex = /\[FILE:\s*(.+?):\s*(.+?)\]/g;
        let match;
        while ((match = fileRegex.exec(decoded)) !== null) {
            const filename = path.basename(match[1]);
            const fullDiskPath = path.join(__dirname, '..', 'uploads', 'chat_files', filename);
            fs.unlink(fullDiskPath, (err) => {
                if (err) console.error('Failed to delete file:', fullDiskPath, err);
                else console.log('Successfully deleted file:', fullDiskPath);
            });
        }
    } catch (e) {
        console.error("Error parsing message for files to delete", e);
    }
}

function broadcastStatus(username, isOnline) {
    const statusMsg = JSON.stringify({
        type: 'status_change',
        username: username,
        ava: isOnline ? "1" : "0"
    });
    clients.forEach((clientSet) => {
        clientSet.forEach(client => {
            if (client.readyState === WebSocket.OPEN) {
                client.send(statusMsg);
            }
        });
    });
}

wss.on('connection', async (ws, req) => {
    const parameters = url.parse(req.url, true);
    const token = parameters.query.token;

    if (!token) {
        ws.close();
        return;
    }

    let username = null;
    try {
        const [rows] = await dbPool.query('SELECT username FROM ws_tokens WHERE token = ?', [token]);
        if (rows.length > 0) {
            username = rows[0].username;
        }
    } catch (e) {
        console.error("Token verification error:", e);
    }

    if (!username) {
        console.log("Connection rejected: Invalid or missing token.");
        ws.close();
        return;
    }

    if (!clients.has(username)) {
        clients.set(username, new Set());
    }
    clients.get(username).add(ws);

    if (clients.get(username).size === 1) {
        broadcastStatus(username, true);
    }

    console.log(`${username} connected. Total connections for user: ${clients.get(username).size}`);

    ws.on('message', async (messageData) => {
        try {
            const data = JSON.parse(messageData);

            if (!dbPool) {
                console.error("No database connection");
                return;
            }

            if (data.type === 'personal') {
                const towho = data.to;
                const tname = data.tname;
                const messageEncoded = data.message;
                const fromwho = username;

                // get next pos
                const [maxRows] = await dbPool.query(`SELECT IFNULL(MAX(CAST(pos AS UNSIGNED)), 0) + 1 AS nextPos FROM \`${tname}\``);
                const nextPos = maxRows[0].nextPos;

                // insert
                await dbPool.query(`INSERT INTO \`${tname}\` (fromwho, message, pos) VALUES (?, ?, ?)`, [fromwho, messageEncoded, nextPos]);

                // get inserted row for exact date
                const [rows] = await dbPool.query(`SELECT * FROM \`${tname}\` WHERE pos = ?`, [nextPos]);
                const insertedRow = rows[0];
                const values = Object.values(insertedRow);
                const date = values[3] || new Date().toISOString().replace('T', ' ').substring(0, 19);

                const responseData = {
                    type: 'personal',
                    tname: tname,
                    ava: "1",
                    date: date,
                    pos: nextPos.toString(),
                    message: formatMessageNode(messageEncoded),
                    rawMessage: decodeURIComponent(messageEncoded),
                    lastwho: fromwho
                };

                const responseStr = JSON.stringify(responseData);

                // Send to sender
                ws.send(responseStr);

                // Send to receiver if connected
                if (clients.has(towho)) {
                    clients.get(towho).forEach(client => {
                        if (client.readyState === WebSocket.OPEN) {
                            client.send(responseStr);
                        }
                    });
                }
            }
            else if (data.type === 'group') {
                const groupId = data.groupId;
                const tname = data.tname;
                const messageEncoded = data.message;
                const fromwho = username;

                // get next pos
                const [maxRows] = await dbPool.query(`SELECT IFNULL(MAX(CAST(pos AS UNSIGNED)), 0) + 1 AS nextPos FROM \`${tname}\``);
                const nextPos = maxRows[0].nextPos;

                // insert
                await dbPool.query(`INSERT INTO \`${tname}\` (fromwho, message, pos) VALUES (?, ?, ?)`, [fromwho, messageEncoded, nextPos]);

                const [rows] = await dbPool.query(`SELECT * FROM \`${tname}\` WHERE pos = ?`, [nextPos]);
                const insertedRow = rows[0];
                const values = Object.values(insertedRow);
                const date = values[3] || new Date().toISOString().replace('T', ' ').substring(0, 19);

                const responseData = {
                    type: 'group',
                    groupId: groupId,
                    tname: tname,
                    date: date,
                    pos: nextPos.toString(),
                    message: formatMessageNode(messageEncoded),
                    rawMessage: decodeURIComponent(messageEncoded),
                    lastwho: fromwho
                };

                const responseStr = JSON.stringify(responseData);

                // First get all group members
                const [groupMembers] = await dbPool.query(`SELECT username FROM \`${tname}_users\``);

                groupMembers.forEach(memberRow => {
                    const memberUsername = memberRow.username;
                    if (clients.has(memberUsername)) {
                        clients.get(memberUsername).forEach(client => {
                            if (client.readyState === WebSocket.OPEN) {
                                client.send(responseStr);
                            }
                        });
                    }
                });
            } else if (data.type === 'destroy_group') {
                const tname = data.tname;
                const destroyMsg = JSON.stringify({ type: 'group_destroyed', tname: tname });
                try {
                    // Broadcast to all connected clients.
                    // The client-side JS will check if they are currently on this group's page
                    // and redirect if they are. We don't query the DB because PHP might have
                    // already dropped the table by the time this executes.
                    for (let [clientUsername, userClients] of clients.entries()) {
                        if (clientUsername !== username) {
                            userClients.forEach(client => {
                                if (client.readyState === WebSocket.OPEN) {
                                    client.send(destroyMsg);
                                }
                            });
                        }
                    }
                } catch (e) {
                    console.error("Error destroying group in WS", e);
                }
            } else if (data.type === 'kick_user') {
                const tname = data.tname;
                const target = data.target;
                const kickMsg = JSON.stringify({ type: 'user_kicked', tname: tname });
                if (clients.has(target)) {
                    clients.get(target).forEach(client => {
                        if (client.readyState === WebSocket.OPEN) {
                            client.send(kickMsg);
                        }
                    });
                }
            } else if (data.type === 'delete_friend') {
                const target = data.target;
                const deleteMsg = JSON.stringify({ type: 'friend_deleted', from: username });
                if (clients.has(target)) {
                    clients.get(target).forEach(client => {
                        if (client.readyState === WebSocket.OPEN) {
                            client.send(deleteMsg);
                        }
                    });
                }
            } else if (data.type === 'check_status') {
                const target = data.target;
                const isOnline = clients.has(target) && clients.get(target).size > 0;
                ws.send(JSON.stringify({
                    type: 'status_change',
                    username: target,
                    ava: isOnline ? "1" : "0"
                }));
            } else if (data.type === 'delete_personal_message') {
                const pos = data.pos;
                const tname = data.tname;
                const towho = data.to;

                const [msgRows] = await dbPool.query(`SELECT message FROM \`${tname}\` WHERE pos = ? AND fromwho = ?`, [pos, username]);
                if (msgRows.length > 0) {
                    deleteFilesFromMessage(msgRows[0].message);
                }

                const [result] = await dbPool.query(`DELETE FROM \`${tname}\` WHERE pos = ? AND fromwho = ?`, [pos, username]);
                if (result.affectedRows > 0) {
                    const deleteMsg = JSON.stringify({ type: 'message_deleted', pos: pos, tname: tname });
                    ws.send(deleteMsg);
                    if (clients.has(towho)) {
                        clients.get(towho).forEach(client => {
                            if (client.readyState === WebSocket.OPEN) {
                                client.send(deleteMsg);
                            }
                        });
                    }
                }
            } else if (data.type === 'delete_group_message') {
                const pos = data.pos;
                const tname = data.tname;

                const [msgRows] = await dbPool.query(`SELECT message FROM \`${tname}\` WHERE pos = ? AND fromwho = ?`, [pos, username]);
                if (msgRows.length > 0) {
                    deleteFilesFromMessage(msgRows[0].message);
                }

                const [result] = await dbPool.query(`DELETE FROM \`${tname}\` WHERE pos = ? AND fromwho = ?`, [pos, username]);
                if (result.affectedRows > 0) {
                    const deleteMsg = JSON.stringify({ type: 'message_deleted', pos: pos, tname: tname });
                    const [groupMembers] = await dbPool.query(`SELECT username FROM \`${tname}_users\``);
                    groupMembers.forEach(memberRow => {
                        const memberUsername = memberRow.username;
                        if (clients.has(memberUsername)) {
                            clients.get(memberUsername).forEach(client => {
                                if (client.readyState === WebSocket.OPEN) {
                                    client.send(deleteMsg);
                                }
                            });
                        }
                    });
                }
            } else if (data.type === 'edit_personal_message') {
                const pos = data.pos;
                const tname = data.tname;
                const towho = data.to;
                const newMessageEncoded = data.new_message;
                const [result] = await dbPool.query(`UPDATE \`${tname}\` SET message = ? WHERE pos = ? AND fromwho = ?`, [newMessageEncoded, pos, username]);
                if (result.affectedRows > 0) {
                    const editMsg = JSON.stringify({ type: 'message_edited', pos: pos, tname: tname, message: formatMessageNode(newMessageEncoded), rawMessage: decodeURIComponent(newMessageEncoded) });
                    ws.send(editMsg);
                    if (clients.has(towho)) {
                        clients.get(towho).forEach(client => {
                            if (client.readyState === WebSocket.OPEN) {
                                client.send(editMsg);
                            }
                        });
                    }
                }
            } else if (data.type === 'edit_group_message') {
                const pos = data.pos;
                const tname = data.tname;
                const newMessageEncoded = data.new_message;
                const [result] = await dbPool.query(`UPDATE \`${tname}\` SET message = ? WHERE pos = ? AND fromwho = ?`, [newMessageEncoded, pos, username]);
                if (result.affectedRows > 0) {
                    const editMsg = JSON.stringify({ type: 'message_edited', pos: pos, tname: tname, message: formatMessageNode(newMessageEncoded), rawMessage: decodeURIComponent(newMessageEncoded) });
                    const [groupMembers] = await dbPool.query(`SELECT username FROM \`${tname}_users\``);
                    groupMembers.forEach(memberRow => {
                        const memberUsername = memberRow.username;
                        if (clients.has(memberUsername)) {
                            clients.get(memberUsername).forEach(client => {
                                if (client.readyState === WebSocket.OPEN) {
                                    client.send(editMsg);
                                }
                            });
                        }
                    });
                }
            }
        } catch (err) {
            console.error("Error processing message:", err);
        }
    });

    ws.on('close', () => {
        if (clients.has(username)) {
            clients.get(username).delete(ws);
            if (clients.get(username).size === 0) {
                clients.delete(username);
                broadcastStatus(username, false);
            }
        }
        console.log(`${username} disconnected.`);
    });
});

const PORT = process.env.PORT || process.env.WS_PORT || 8300;
server.listen(PORT, () => {
    console.log(`WebSocket Server is listening on port ${PORT}`);
});
