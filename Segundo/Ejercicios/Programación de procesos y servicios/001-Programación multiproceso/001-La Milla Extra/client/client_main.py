# client/client_main.py
import json
import os
import socket
import sys
import threading
import time
import tkinter as tk
from tkinter import messagebox, scrolledtext, ttk

# Añado el path del servidor al sys.path para importar los módulos
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from client_thread import ClientThread
from crypto_utils import generate_rsa_key_pair


class ChatClient:
    def __init__(self):
        self.root = tk.Tk()
        self.root.title("Chat Seguro - Cliente")
        self.root.geometry("700x550")
        self.root.resizable(True, True)

        # Variables del cliente
        self.client_thread = None
        self.connected = False

        # Genero par de claves RSA para este cliente
        self.private_key, self.public_key = generate_rsa_key_pair()

        # Creo la interfaz gráfica
        self.setup_gui()

    def setup_gui(self):
        # Frame principal
        main_frame = ttk.Frame(self.root, padding="10")
        main_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))

        # Configuro el grid para que sea redimensionable
        self.root.columnconfigure(0, weight=1)
        self.root.rowconfigure(0, weight=1)
        main_frame.columnconfigure(0, weight=1)
        main_frame.rowconfigure(1, weight=1)

        # Frame de conexión
        conn_frame = ttk.LabelFrame(
            main_frame, text="Configuración de Conexión", padding="10"
        )
        conn_frame.grid(row=0, column=0, sticky=(tk.W, tk.E), pady=(0, 10))
        conn_frame.columnconfigure(1, weight=1)

        ttk.Label(conn_frame, text="Servidor:").grid(
            row=0, column=0, padx=5, sticky=tk.W
        )
        self.server_entry = ttk.Entry(conn_frame, width=20)
        self.server_entry.grid(row=0, column=1, padx=5, sticky=(tk.W, tk.E))
        self.server_entry.insert(0, "127.0.0.1")

        ttk.Label(conn_frame, text="Puerto:").grid(row=0, column=2, padx=5, sticky=tk.W)
        self.port_entry = ttk.Entry(conn_frame, width=10)
        self.port_entry.grid(row=0, column=3, padx=5)
        self.port_entry.insert(0, "5000")

        # Frame de botones de conexión
        button_frame = ttk.Frame(conn_frame)
        button_frame.grid(row=1, column=0, columnspan=4, pady=(10, 0))

        self.connect_btn = ttk.Button(
            button_frame, text="🔗 Conectar", command=self.connect_to_server
        )
        self.connect_btn.pack(side=tk.LEFT, padx=5)

        self.disconnect_btn = ttk.Button(
            button_frame,
            text="🔌 Desconectar",
            command=self.disconnect_from_server,
            state=tk.DISABLED,
        )
        self.disconnect_btn.pack(side=tk.LEFT, padx=5)

        # Frame del chat
        chat_frame = ttk.LabelFrame(main_frame, text="Área de Chat", padding="10")
        chat_frame.grid(row=1, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))
        chat_frame.columnconfigure(0, weight=1)
        chat_frame.rowconfigure(0, weight=1)

        # Área de mensajes (scrolled text)
        self.chat_area = scrolledtext.ScrolledText(
            chat_frame,
            wrap=tk.WORD,
            state=tk.DISABLED,
            height=15,
            font=("Consolas", 10),
            bg="#f0f0f0",
        )
        self.chat_area.grid(
            row=0, column=0, columnspan=2, sticky=(tk.W, tk.E, tk.N, tk.S), pady=(0, 10)
        )

        # Configuro colores para diferentes tipos de mensajes
        self.chat_area.tag_config("me", foreground="blue", font=("Arial", 10, "bold"))
        self.chat_area.tag_config("other", foreground="green", font=("Arial", 10))
        self.chat_area.tag_config(
            "system", foreground="orange", font=("Arial", 10, "italic")
        )

        # Frame de entrada de mensajes
        input_frame = ttk.Frame(chat_frame)
        input_frame.grid(row=1, column=0, columnspan=2, sticky=(tk.W, tk.E))
        input_frame.columnconfigure(0, weight=1)

        self.message_entry = ttk.Entry(input_frame, width=50, font=("Arial", 10))
        self.message_entry.grid(row=0, column=0, padx=(0, 5), sticky=(tk.W, tk.E))
        self.message_entry.bind("<Return>", lambda e: self.send_message())

        self.send_btn = ttk.Button(
            input_frame, text="📤 Enviar", command=self.send_message, state=tk.DISABLED
        )
        self.send_btn.grid(row=0, column=1)

        # Frame de estado
        status_frame = ttk.Frame(main_frame)
        status_frame.grid(row=2, column=0, sticky=(tk.W, tk.E), pady=(10, 0))

        self.status_var = tk.StringVar(value="🔴 Desconectado")
        status_label = ttk.Label(
            status_frame, textvariable=self.status_var, font=("Arial", 9)
        )
        status_label.pack(side=tk.LEFT)

        # Info de seguridad
        security_info = ttk.Label(
            status_frame, text="🔒 Conexión cifrada con RSA+AES", font=("Arial", 9)
        )
        security_info.pack(side=tk.RIGHT)

    def connect_to_server(self):
        try:
            server = self.server_entry.get().strip()
            port_str = self.port_entry.get().strip()

            if not server:
                messagebox.showerror(
                    "Error", "Por favor ingrese una dirección de servidor"
                )
                return

            if not port_str:
                messagebox.showerror("Error", "Por favor ingrese un puerto")
                return

            port = int(port_str)
            if port < 1 or port > 65535:
                messagebox.showerror("Error", "El puerto debe estar entre 1 y 65535")
                return

            # Creo e inicio el hilo del cliente
            self.client_thread = ClientThread(
                server, port, self.public_key, self.private_key
            )
            self.client_thread.set_callbacks(
                self.on_message_received, self.on_connection_status
            )

            if self.client_thread.connect():
                self.client_thread.start()
                self.connected = True
                self.update_ui_state(connected=True)
                self.add_message("Conectado al servidor", "system")
                self.status_var.set(f"🟢 Conectado a {server}:{port}")
            else:
                messagebox.showerror("Error", "No se pudo conectar al servidor")

        except ValueError:
            messagebox.showerror("Error", "El puerto debe ser un número válido")
        except Exception as e:
            messagebox.showerror("Error", f"Error de conexión: {e}")

    def disconnect_from_server(self):
        if self.client_thread:
            self.client_thread.disconnect()
            self.client_thread = None

        self.connected = False
        self.update_ui_state(connected=False)
        self.add_message("Desconectado del servidor", "system")
        self.status_var.set("🔴 Desconectado")

    def send_message(self):
        if self.connected and self.client_thread:
            message = self.message_entry.get().strip()
            if message:
                self.client_thread.send_message(message)
                self.add_message(message, "me")  # Muestro mi propio mensaje
                self.message_entry.delete(0, tk.END)
            else:
                messagebox.showwarning("Advertencia", "Por favor ingrese un mensaje")

    def update_ui_state(self, connected):
        if connected:
            self.connect_btn.config(state=tk.DISABLED)
            self.disconnect_btn.config(state=tk.NORMAL)
            self.send_btn.config(state=tk.NORMAL)
            self.message_entry.config(state=tk.NORMAL)
            self.server_entry.config(state=tk.DISABLED)
            self.port_entry.config(state=tk.DISABLED)
        else:
            self.connect_btn.config(state=tk.NORMAL)
            self.disconnect_btn.config(state=tk.DISABLED)
            self.send_btn.config(state=tk.DISABLED)
            self.message_entry.config(state=tk.DISABLED)
            self.server_entry.config(state=tk.NORMAL)
            self.port_entry.config(state=tk.NORMAL)

    def on_message_received(self, message):
        # Callback para mensajes recibidos (se ejecuta en el hilo principal)
        self.add_message(message, "other")

    def on_connection_status(self, status):
        # Callback para cambios de estado
        self.status_var.set(status)

    def add_message(self, message, msg_type="me"):
        # Añado mensaje al área de chat
        self.chat_area.config(state=tk.NORMAL)

        timestamp = time.strftime("%H:%M:%S")

        if msg_type == "me":
            self.chat_area.insert(tk.END, f"[{timestamp}] Tú: {message}\n", "me")
        elif msg_type == "other":
            self.chat_area.insert(tk.END, f"[{timestamp}] {message}\n", "other")
        else:  # system
            self.chat_area.insert(
                tk.END, f"[{timestamp}] *** {message} ***\n", "system"
            )

        self.chat_area.config(state=tk.DISABLED)
        self.chat_area.see(tk.END)

    def on_closing(self):
        # Manejo el cierre de la ventana
        if self.connected:
            self.disconnect_from_server()
        self.root.destroy()

    def run(self):
        # Inicio la aplicación
        self.root.protocol("WM_DELETE_WINDOW", self.on_closing)

        # Mensaje de bienvenida
        self.add_message("Bienvenido al Chat Seguro", "system")
        self.add_message(
            "Ingrese la dirección del servidor y puerto, luego haga clic en Conectar",
            "system",
        )

        self.root.mainloop()


if __name__ == "__main__":
    # Verifico que las dependencias estén disponibles
    try:
        from Crypto import Random
        from Crypto.Cipher import AES, PKCS1_OAEP
        from Crypto.PublicKey import RSA
    except ImportError:
        messagebox.showerror(
            "Error",
            "PyCryptodome no está instalado.\n"
            "Por favor instálelo con: pip install pycryptodome",
        )
        sys.exit(1)

    client = ChatClient()
    client.run()
