import socket
import threading

# Paramètres du serveur
HOST = '127.0.0.1'  # Écoute sur toutes les interfaces disponibles
PORT = 8000

# Dictionnaire pour stocker les connexions des clients
clients = {}

# Fonction pour gérer les messages entrants d'un client
def handle_client(client_socket, username):
    while True:
        try:
            message = client_socket.recv(1024).decode('utf-8')
            if not message:
                print(f"{username} a quitté le chat.")
                del clients[username]
                broadcast(f"{username} a quitté le chat.")
                break
            print(f"{username}: {message}")
            broadcast(f"{username}: {message}")
        except:
            break

# Fonction pour diffuser un message à tous les clients
def broadcast(message):
    for client in clients:
        client_socket = clients[client]
        try:
            client_socket.send(message.encode('utf-8'))
        except:
            pass

# Fonction principale du serveur
def main():
    server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server.bind((HOST, PORT))
    server.listen(5)

    print(f"Serveur de chat en écoute sur {HOST}:{PORT}")

    while True:
        client_socket, client_addr = server.accept()
        username = client_socket.recv(1024).decode('utf-8')
        clients[username] = client_socket

        print(f"{username} a rejoint le chat depuis {client_addr}")
        broadcast(f"{username} a rejoint le chat.")

        # Démarrer un thread pour gérer les messages du client
        client_thread = threading.Thread(target=handle_client, args=(client_socket, username))
        client_thread.start()

if __name__ == '__main__':
    main()
