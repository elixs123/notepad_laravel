const { Server } = require("socket.io");
const io = new Server(3000, { cors: { origin: "*" } });

let users = {};

io.on("connection", (socket) => {
    console.log("a user connected");
    socket.on("user:join", ({ noteId, user }) => {
        if (!users[noteId]) users[noteId] = [];
        users[noteId].push(user);
        io.emit("users:update", users[noteId]);
    });

    socket.on("document:update", (data) => {
        socket.broadcast.emit("document:update", data);
    });

    socket.on("user:typing", (data) => {
        socket.broadcast.emit("user:typing", data);
    });

    socket.on("disconnect", () => {
        
        console.log("doslolo je do disconnecta");
    });
});

console.log("Socket.IO server running at http://localhost:3000/");