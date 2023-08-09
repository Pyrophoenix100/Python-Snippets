import socket
HOST = "0.0.0.0"
inp = input("Port [2000] > ")
if (inp == ""):
    PORT = 2000 #Change as needed
else:
    PORT = int(inp)
print(f"Starting on port {PORT}")
with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
    s.bind((HOST, PORT))
    s.listen()
    conn, addr = s.accept()
    with conn:
        print(f"Connected by {addr}")
        while True:
            data = conn.recv(1024)
            if not data:
                break
            print(f"Received {data}")
            conn.sendall(data)
