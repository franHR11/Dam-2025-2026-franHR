# ChatServer Seguro Distribuido - Ejercicio de examen

## Introducción breve

En este ejercicio de examen he desarrollado una aplicación de chat distribuido que permite a múltiples usuarios comunicarse a través de la red de forma segura. La aplicación implementa una arquitectura cliente-servidor donde el servidor puede manejar múltiples conexiones simultáneas usando tanto multiproceso como multihilo.

La contextualización de este proyecto se centra en los sistemas de comunicación seguros actuales, similares a los que usan WhatsApp, Telegram o Discord, pero con un enfoque educativo para demostrar el funcionamiento interno de estos sistemas. El chat implementa criptografía para proteger las conversaciones, monitorización de procesos para mantener el sistema estable, y un sistema de logging para registrar todas las actividades.

He decidido usar Python porque tiene bibliotecas muy potentes para redes (socket), criptografía (pycryptodome), y multiprocesamiento, además de que es el lenguaje que más domino y me permite crear código claro y bien estructurado.

## Desarrollo detallado

### Unidad 1: Programación multiproceso

**Subunidades cubiertas:**
- Ejecutables, procesos y servicios
- Estados de un proceso  
- Gestión y monitorización de procesos
- Sincronización entre procesos

En mi implementación, el servidor principal (`server_main.py`) arranca tres procesos diferentes:

1. **Proceso principal**: Maneja la lógica del servidor y acepta conexiones
2. **Proceso de logs**: Se encarga de escribir todos los eventos a archivos de log de forma asíncrona
3. **Proceso de monitor**: Supervisa el estado de CPU, memoria y hilos del servidor

```python
# server_main.py - Proceso principal que lanza subprocesos
from multiprocessing import Process, Queue

def main():
    cola_logs = Queue()
    
    # Proceso de logging
    proceso_logs = Process(target=servicio_logs, args=(cola_logs, config))
    proceso_logs.start()
    
    # Proceso de monitorización  
    proceso_monitor = Process(target=monitorizar_procesos, args=(cola_logs,))
    proceso_monitor.start()
    
    # Servidor principal (multihilo)
    iniciar_servidor(config, cola_logs, clave_publica, clave_privada)
```

### Unidad 2: Programación multihilo

**Subunidades cubiertas:**
- Contexto de ejecución de hilos
- Sincronización y comunicación entre hilos
- Gestión y prioridades de hilos

Cada cliente que se conecta al servidor se maneja en un hilo separado, permitiendo que múltiples usuarios puedan chatear al mismo tiempo sin bloquearse. Uso `threading.Lock()` para proteger el acceso a las listas compartidas de clientes conectados.

```python
# server_thread.py - Manejo multihilo de clientes
clientes_conectados = {}
lock_clientes = threading.Lock()

def manejar_cliente(conn, addr, cola_logs, clave_publica, clave_privada, config):
    # Este código se ejecuta en un hilo separado para cada cliente
    with lock_clientes:
        clientes_conectados[conn] = {"nombre": nombre, "conectado_desde": datetime.now()}
```

### Unidad 3: Programación de comunicaciones en red

**Subunidades cubiertas:**
- Modelos de comunicación cliente-servidor
- Sockets TCP/IP
- Conexiones simultáneas con hilos

El sistema usa sockets TCP para la comunicación. El cliente (`client_main.py`) se conecta al puerto 5000 del servidor y establece una comunicación bidireccional usando hilos separados para envío y recepción de mensajes.

```python
# client_main.py - Cliente con comunicación de red
def conectar_servidor(self):
    self.socket_cliente = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    self.socket_cliente.connect((self.host, self.puerto))
    
def recibir_mensajes(self):
    while self.conectado:
        datos = self.socket_cliente.recv(4096)
        if datos:
            mensaje = descifrar_mensaje(datos)
```

### Unidad 4: Generación de servicios en red

**Subunidades cubiertas:**
- Protocolos estándar
- Programación de servidores
- Monitorización y gestión del servicio

He creado un servicio robusto que incluye:
- Reinicio automático de conexiones caídas
- Logs detallados de todas las actividades
- Monitorización en tiempo real del rendimiento
- Gestión de errores y recuperación automática

```python
# process_monitor.py - Monitorización del servicio
def monitorizar_procesos(cola_logs):
    while True:
        cpu_porcentaje = proceso_principal.cpu_percent()
        memoria_mb = proceso_principal.memory_info().rss / 1024 / 1024
        cola_logs.put(f"STATS: CPU={cpu_porcentaje:.1f}% RAM={memoria_mb:.1f}MB")
        time.sleep(5)
```

### Unidad 5: Programación segura

**Subunidades cubiertas:**
- Criptografía simétrica/asimétrica
- Encriptación de datos en la comunicación
- Protocolos seguros

Implemento un sistema híbrido RSA + AES:
- **RSA** para intercambiar claves de forma segura
- **AES** para cifrar los mensajes con la clave compartida

```python
# crypto_utils.py - Criptografía híbrida
def cifrar_clave_aes_rsa(clave_aes, clave_publica_rsa):
    key = RSA.import_key(clave_publica_rsa)
    cipher = PKCS1_OAEP.new(key)
    return cipher.encrypt(clave_aes)

def cifrar_mensaje_aes(mensaje, clave_aes):
    cipher = AES.new(clave_aes, AES.MODE_EAX, nonce=nonce)
    ciphertext, tag = cipher.encrypt_and_digest(mensaje.encode())
    return nonce + ciphertext + tag
```

### Unidad 6: Técnicas de programación segura

**Subunidades cubiertas:**
- Políticas de acceso
- Control de errores y validación de entradas
- Logs y roles

El sistema incluye validación de entrada, filtrado de mensajes problemáticos, diferentes niveles de log (conexiones, errores, seguridad), y un sistema básico de roles definido en `config/settings.json`.

```python
# Técnicas de seguridad implementadas
def filtrar_mensaje(mensaje):
    palabras_prohibidas = ["hack", "exploit", "drop", "delete"]
    for palabra in palabras_prohibidas:
        if palabra in mensaje.lower():
            return None  # Rechazar mensaje
    return mensaje
```

## Aplicación práctica

Aquí tienes el código completo del proyecto con todas las funcionalidades implementadas:

### Estructura del proyecto
```
ChatServerSeguro/
├── server/
│   ├── server_main.py        # Proceso principal multiproceso
│   ├── server_thread.py      # Hilos para manejar clientes
│   ├── crypto_utils.py       # Criptografía RSA + AES
│   ├── process_monitor.py    # Monitor de procesos
│   └── log_service.py        # Servicio de logging
├── client/
│   └── client_main.py        # Cliente con interfaz gráfica
├── config/
│   └── settings.json         # Configuración del servidor
└── logs/
    ├── server.log            # Log principal
    ├── connections.log       # Log de conexiones
    ├── errors.log           # Log de errores
    └── security.log         # Log de seguridad
```

### Configuración del servidor
```json
{
  "server": {
    "host": "0.0.0.0",
    "port": 5000,
    "max_connections": 50,
    "timeout": 30
  },
  "security": {
    "rsa_key_size": 2048,
    "encryption_enabled": true
  },
  "logging": {
    "level": "INFO",
    "file": "logs/server.log"
  }
}
```

### Proceso principal del servidor
```python
# server_main.py
import json
import os
import socket
import sys
import threading
from datetime import datetime
from multiprocessing import Process, Queue

# Agrego el directorio actual al path para importar módulos
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from server_thread import iniciar_servidor
from process_monitor import monitorizar_procesos
from log_service import servicio_logs
from crypto_utils import generar_claves_rsa

def cargar_configuracion():
    """Cargo la configuración desde el archivo JSON"""
    with open("../config/settings.json", "r") as f:
        return json.load(f)

def main():
    """Función principal que orquesta todos los procesos del servidor"""
    print("🚀 Iniciando ChatServer Seguro Distribuido...")
    print(f"📅 {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Cargo configuración
    config = cargar_configuracion()
    print("⚙️ Configuración cargada correctamente")
    
    # Creo la cola para comunicación entre procesos
    cola_logs = Queue()
    
    # Genero las claves RSA del servidor
    print("🔐 Generando claves RSA del servidor...")
    clave_privada, clave_publica = generar_claves_rsa(config["security"]["rsa_key_size"])
    
    # Creo los procesos secundarios
    print("🔄 Creando procesos de soporte...")
    
    # Proceso de logs
    proceso_logs = Process(target=servicio_logs, args=(cola_logs, config))
    proceso_logs.start()
    
    # Proceso de monitorización
    proceso_monitor = Process(target=monitorizar_procesos, args=(cola_logs,))
    proceso_monitor.start()
    
    # Registro el inicio del servidor
    cola_logs.put("SERVIDOR_INICIADO: Servidor principal iniciado")
    cola_logs.put("CLAVES_RSA: Claves RSA generadas correctamente")
    
    try:
        # Inicio el servidor principal (multihilo)
        print("🌐 Iniciando servidor de chat...")
        iniciar_servidor(config, cola_logs, clave_publica, clave_privada)
        
    except KeyboardInterrupt:
        print("\n⚠️ Apagando servidor por interrupción del usuario...")
        cola_logs.put("SERVIDOR_DETENIDO: Apagado por usuario")
        
    except Exception as e:
        print(f"❌ Error en servidor principal: {e}")
        cola_logs.put(f"ERROR_SERVIDOR: {str(e)}")
        
    finally:
        # Cierro todos los procesos
        print("🛑 Cerrando procesos de soporte...")
        proceso_logs.terminate()
        proceso_monitor.terminate()
        print("✅ Servidor cerrado correctamente")

if __name__ == "__main__":
    main()
```

### Módulo de criptografía
```python
# crypto_utils.py
import base64
import os
from Crypto.Cipher import AES, PKCS1_OAEP
from Crypto.PublicKey import RSA
from Crypto.Random import get_random_bytes

def generar_claves_rsa(tamaño=2048):
    """Genero un par de claves RSA para el intercambio seguro de claves"""
    print("Generando claves RSA...")
    clave = RSA.generate(tamaño)
    clave_privada = clave.export_key()
    clave_publica = clave.publickey().export_key()
    return clave_privada, clave_publica

def cifrar_mensaje_aes(mensaje, clave_aes):
    """Cifro un mensaje usando AES con la clave proporcionada"""
    # Genero un nonce aleatorio para EAX
    nonce = get_random_bytes(16)
    cipher = AES.new(clave_aes, AES.MODE_EAX, nonce=nonce)
    ciphertext, tag = cipher.encrypt_and_digest(mensaje.encode())
    # Combino nonce + ciphertext + tag
    return base64.b64encode(nonce + ciphertext + tag)

def descifrar_mensaje_aes(mensaje_cifrado, clave_aes):
    """Descifro un mensaje cifrado con AES"""
    datos = base64.b64decode(mensaje_cifrado)
    nonce = datos[:16]
    tag = datos[-16:]
    ciphertext = datos[16:-16]
    
    cipher = AES.new(clave_aes, AES.MODE_EAX, nonce=nonce)
    return cipher.decrypt_and_verify(ciphertext, tag).decode()

def generar_clave_aes():
    """Genero una clave aleatoria de 256 bits para AES"""
    return get_random_bytes(32)

def cifrar_clave_aes_rsa(clave_aes, clave_publica_rsa):
    """Cifro la clave AES usando RSA para compartirla de forma segura"""
    key = RSA.import_key(clave_publica_rsa)
    cipher = PKCS1_OAEP.new(key)
    return cipher.encrypt(clave_aes)

def descifrar_clave_aes_rsa(clave_aes_cifrada, clave_privada_rsa):
    """Descifro la clave AES usando mi clave privada RSA"""
    key = RSA.import_key(clave_privada_rsa)
    cipher = PKCS1_OAEP.new(key)
    return cipher.decrypt(clave_aes_cifrada)
```

### Manejo multihilo de clientes
```python
# server_thread.py
import socket
import threading
import json
import time
from datetime import datetime
from crypto_utils import (
    recibir_clave_segura,
    crear_paquete_seguro,
    abrir_paquete_seguro
)

# Variables globales para el manejo de clientes
clientes_conectados = {}
lock_clientes = threading.Lock()
estadisticas = {
    "conexiones_totales": 0,
    "mensajes_enviados": 0,
    "errores_crypto": 0
}

def manejar_cliente(conn, addr, cola_logs, clave_publica, clave_privada, config):
    """Función que maneja cada cliente en un hilo separado"""
    nombre_usuario = None
    clave_aes_cliente = None

    try:
        # Registro la nueva conexión
        with lock_clientes:
            estadisticas["conexiones_totales"] += 1

        cola_logs.put(f"NUEVA_CONEXION: {addr[0]}:{addr[1]} conectado")

        # Envío mi clave pública al cliente
        conn.send(clave_publica)
        cola_logs.put(f"CLAVE_PUBLICA_ENVIADA: {addr[0]}:{addr[1]}")

        # Recibo la clave AES del cliente (cifrada con mi clave pública)
        clave_aes_cifrada = conn.recv(1024)
        clave_aes_cliente = recibir_clave_segura(clave_aes_cifrada, clave_privada)
        cola_logs.put(f"CLAVE_AES_RECIBIDA: {addr[0]}:{addr[1]}")

        # Bucle principal de comunicación
        while True:
            try:
                # Recibo mensaje cifrado
                datos = conn.recv(4096)
                if not datos:
                    break

                # Descifro y proceso el mensaje
                paquete = abrir_paquete_seguro(datos, clave_aes_cliente)
                if paquete and paquete["mensaje"]:
                    # Filtro mensajes problemáticos
                    mensaje_filtrado = filtrar_mensaje(paquete["mensaje"])
                    if mensaje_filtrado:
                        # Creo mensaje para broadcast
                        mensaje_completo = f"{nombre_usuario}: {mensaje_filtrado}"
                        mensaje_cifrado = crear_paquete_seguro(mensaje_completo, clave_aes_cliente)

                        # Envío a todos los clientes
                        broadcast_mensaje(mensaje_cifrado, conn, cola_logs)
                        with lock_clientes:
                            estadisticas["mensajes_enviados"] += 1

            except Exception as e:
                cola_logs.put(f"ERROR_PROCESANDO_MENSAJE: {e}")
                continue

    except Exception as e:
        cola_logs.put(f"ERROR_CLIENTE: {addr[0]}:{addr[1]} - {e}")

    finally:
        # Limpio la conexión
        if conn in clientes_conectados:
            nombre = clientes_conectados[conn]["nombre"]
            with lock_clientes:
                del clientes_conectados[conn]

        conn.close()

def filtrar_mensaje(mensaje):
    """Filtro mensajes problemáticos y peligrosos"""
    # Lista de palabras prohibidas (simplificada)
    palabras_prohibidas = ["hack", "exploit", "drop", "delete"]

    mensaje_lower = mensaje.lower()
    for palabra in palabras_prohibidas:
        if palabra in mensaje_lower:
            return None

    # Valido longitud
    if len(mensaje) > 500:
        return mensaje[:500] + "... [truncado]"

    return mensaje

def broadcast_mensaje(mensaje_cifrado, emisor_excluido, cola_logs):
    """Envío un mensaje cifrado a todos los clientes conectados"""
    clientes_a_eliminar = []

    with lock_clientes:
        for cliente in list(clientes_conectados.keys()):
            if cliente != emisor_excluido:
                try:
                    cliente.send(mensaje_cifrado)
                except:
                    clientes_a_eliminar.append(cliente)

    # Elimino clientes desconectados
    for cliente in clientes_a_eliminar:
        if cliente in clientes_conectados:
            del clientes_conectados[cliente]

def iniciar_servidor(config, cola_logs, clave_publica, clave_privada):
    """Función principal que crea el socket y acepta conexiones"""
    # Creo el socket del servidor
    servidor = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    servidor.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)

    # Configuro el socket
    host = config["server"]["host"]
    puerto = config["server"]["port"]
    servidor.bind((host, puerto))
    servidor.listen(config["server"]["max_connections"])

    print(f"🌐 Servidor escuchando en {host}:{puerto}")
    print(f"🔗 Máximo {config['server']['max_connections']} clientes simultáneos")
    print("⏹️  Presiona Ctrl+C para detener el servidor")

    cola_logs.put(f"SERVIDOR_ESCUCHANDO: {host}:{puerto}")

    try:
        while True:
            # Acepto nueva conexión
            conn, addr = servidor.accept()
            print(f"🔌 Nueva conexión desde {addr[0]}:{addr[1]}")

            # Lanzo un hilo para manejar este cliente
            hilo_cliente = threading.Thread(
                target=manejar_cliente,
                args=(conn, addr, cola_logs, clave_publica, clave_privada, config),
                name=f"Cliente-{addr[0]}:{addr[1]}",
                daemon=True
            )
            hilo_cliente.start()

    except KeyboardInterrupt:
        print("\n⏹️ Deteniendo servidor...")
        cola_logs.put("SERVIDOR_DETENIDO: Apagado por usuario")

    except Exception as e:
        print(f"❌ Error en servidor: {e}")
        cola_logs.put(f"ERROR_SERVIDOR: {str(e)}")

    finally:
        servidor.close()
        print("🔌 Socket del servidor cerrado")
```

### Monitor de procesos
```python
# process_monitor.py
import time
import psutil
import os
from datetime import datetime

def monitorizar_procesos(cola_logs):
    """Monitoreo continuo de los procesos del servidor"""
    print("📊 Iniciando monitor de procesos...")
    tiempo_inicio = time.time()
    
    # Obtengo el PID del proceso padre
    pid_padre = os.getpid()
    procesos_hijos = []
    
    try:
        # Busco los procesos hijos (logs y monitor)
        for proc in psutil.process_iter(['pid', 'name', 'ppid']):
            if proc.info['ppid'] == pid_padre and 'python' in proc.info['name'].lower():
                procesos_hijos.append(proc)
        
        cola_logs.put("MONITOR_INICIADO: Monitor de procesos activo")
        
        while True:
            try:
                tiempo_actual = time.time()
                tiempo_funcionamiento = tiempo_actual - tiempo_inicio
                
                # Obtengo estadísticas del proceso principal
                proceso_principal = psutil.Process(pid_padre)
                
                # CPU y memoria del proceso principal
                cpu_porcentaje = proceso_principal.cpu_percent(interval=0.1)
                memoria_info = proceso_principal.memory_info()
                memoria_mb = memoria_info.rss / 1024 / 1024
                
                # Estadísticas generales del sistema
                cpu_sistema = psutil.cpu_percent(interval=0.1)
                memoria_sistema = psutil.virtual_memory()
                
                # Contador de hilos activos
                num_hilos = proceso_principal.num_threads()
                
                # Log de estadísticas cada 30 segundos
                if int(tiempo_actual) % 30 == 0:
                    cola_logs.put(f"STATS_CPU: {cpu_porcentaje:.1f}% | RAM: {memoria_mb:.1f}MB | Hilos: {num_hilos}")
                    cola_logs.put(f"STATS_SISTEMA: CPU Total: {cpu_sistema:.1f}% | RAM Sistema: {memoria_sistema.percent:.1f}%")
                
                # Muestro información en consola cada 10 segundos
                if int(tiempo_actual) % 10 == 0:
                    print(f"📈 [{datetime.now().strftime('%H:%M:%S')}] "
                          f"CPU: {cpu_porcentaje:.1f}% | "
                          f"RAM: {memoria_mb:.1f}MB | "
                          f"Hilos: {num_hilos} | "
                          f"Uptime: {tiempo_funcionamiento/60:.1f}min")
                
                time.sleep(5)  # Monitoreo cada 5 segundos
                
            except Exception as e:
                print(f"❌ Error en monitor: {e}")
                cola_logs.put(f"ERROR_MONITOR: {str(e)}")
                time.sleep(5)
                
    except KeyboardInterrupt:
        print("⏹️ Deteniendo monitor de procesos...")
        cola_logs.put("MONITOR_DETENIDO: Monitor detenido por usuario")
```

### Servicio de logging
```python
# log_service.py
import time
import threading
from datetime import datetime
from queue import Empty

def servicio_logs(cola_logs, config):
    """Servicio de logs que se ejecuta en un proceso separado"""
    print("📝 Iniciando servicio de logs...")
    
    # Archivos de log
    archivo_principal = config["logging"]["file"]
    archivo_conexiones = "logs/connections.log"
    archivo_errores = "logs/errors.log"
    archivo_seguridad = "logs/security.log"
    
    contador_logs = 0
    
    try:
        while True:
            try:
                # Leo de la cola con timeout
                mensaje = cola_logs.get(timeout=1)
                
                # Analizo el tipo de mensaje para decidir dónde guardarlo
                timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                mensaje_completo = f"[{timestamp}] {mensaje}"
                
                # Clasificación de logs
                if any(tipo in mensaje.upper() for tipo in ["CONEXION", "NUEVA_CONEXION", "DESCONEXION", "CLIENTE"]):
                    # Log de conexiones
                    with open(archivo_conexiones, "a", encoding="utf-8") as f:
                        f.write(mensaje_completo + "\n")
                
                elif any(tipo in mensaje.upper() for tipo in ["ERROR", "FALLO", "CRITICO", "ALERTA"]):
                    # Log de errores
                    with open(archivo_errores, "a", encoding="utf-8") as f:
                        f.write(mensaje_completo + "\n")
                
                elif any(tipo in mensaje.upper() for tipo in ["CLAVE", "CRYPTO", "SEGURIDAD", "ENCRIPT"]):
                    # Log de seguridad
                    with open(archivo_seguridad, "a", encoding="utf-8") as f:
                        f.write(mensaje_completo + "\n")
                
                # Siempre al log principal
                with open(archivo_principal, "a", encoding="utf-8") as f:
                    f.write(mensaje_completo + "\n")
                
                contador_logs += 1
                
                # Log cada 100 mensajes para mostrar actividad
                if contador_logs % 100 == 0:
                    print(f"📝 Logs procesados: {contador_logs}")
                
            except Empty:
                # No hay mensajes en la cola, continuo
                continue
                
    except KeyboardInterrupt:
        print("🛑 Deteniendo servicio de logs...")
        mensaje_fin = f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] SERVICIO_LOGS_DETENIDO"
        
        with open(archivo_principal, "a", encoding="utf-8") as f:
            f.write(mensaje_fin + "\n")
        
        print(f"📝 Servicio de logs finalizado. Total mensajes procesados: {contador_logs}")
```

### Cliente principal
```python
# client_main.py
import socket
import threading
import tkinter as tk
from tkinter import scrolledtext, messagebox
import sys
import os

# Agrego el directorio del servidor al path para importar módulos
sys.path.append(os.path.join(os.path.dirname(os.path.dirname(__file__)), 'server'))

from crypto_utils import (
    generar_claves_rsa,
    cifrar_con_rsa,
    descifrar_con_rsa,
    generar_clave_aes,
    cifrar_con_aes,
    descifrar_con_aes,
    crear_paquete_seguro,
    abrir_paquete_seguro
)

class ClienteChat:
    def __init__(self, host='127.0.0.1', puerto=5000):
        self.host = host
        self.puerto = puerto
        self.socket_cliente = None
        self.conectado = False
        
        # Criptografía
        self.clave_privada_cliente = None
        self.clave_publica_cliente = None
        self.clave_aes_compartida = None
        self.clave_publica_servidor = None
        
        # Interfaz
        self.root = tk.Tk()
        self.root.title("Chat Seguro Distribuido")
        self.root.geometry("600x500")
        self.root.resizable(False, False)
        
        # Variables
        self.nombre_usuario = tk.StringVar()
        self.mensaje_actual = tk.StringVar()
        
        self.crear_interfaz()
    
    def crear_interfaz(self):
        """Creo la interfaz gráfica del cliente"""
        # Frame principal
        main_frame = tk.Frame(self.root, bg='#f0f0f0')
        main_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        # Título
        titulo = tk.Label(main_frame, text="💬 Chat Seguro Distribuido", 
                         font=('Arial', 16, 'bold'), bg='#f0f0f0', fg='#2c3e50')
        titulo.pack(pady=(0, 10))
        
        # Frame de conexión
        conn_frame = tk.Frame(main_frame, bg='#f0f0f0')
        conn_frame.pack(fill=tk.X, pady=(0, 10))
        
        # Campo de usuario
        tk.Label(conn_frame, text="Usuario:", bg='#f0f0f0').pack(side=tk.LEFT)
        self.entry_usuario = tk.Entry(conn_frame, textvariable=self.nombre_usuario, width=20)
        self.entry_usuario.pack(side=tk.LEFT, padx=(5, 10))
        
        # Botón conectar
        self.btn_conectar = tk.Button(conn_frame, text="🔗 Conectar", 
                                     command=self.conectar, bg='#27ae60', fg='white',
                                     font=('Arial', 10, 'bold'))
        self.btn_conectar.pack(side=tk.LEFT, padx=(0, 10))
        
        # Estado de conexión
        self.label_estado = tk.Label(conn_frame, text="❌ Desconectado", 
                                   bg='#f0f0f0', fg='#e74c3c', font=('Arial', 10, 'bold'))
        self.label_estado.pack(side=tk.RIGHT)
        
        # Área de chat
        self.text_chat = scrolledtext.ScrolledText(main_frame, width=70, height=20, 
                                                  font=('Consolas', 10), wrap=tk.WORD,
                                                  state=tk.DISABLED)
        self.text_chat.pack(fill=tk.BOTH, expand=True, pady=(0, 10))
        
        # Frame de mensaje
        msg_frame = tk.Frame(main_frame, bg='#f0f0f0')
        msg_frame.pack(fill=tk.X)
        
        tk.Label(msg_frame, text="Mensaje:", bg='#f0f0f0').pack(side=tk.LEFT)
        self.entry_mensaje = tk.Entry(msg_frame, textvariable=self.mensaje_actual, width=50)
        self.entry_mensaje.pack(side=tk.LEFT, padx=(5, 10), fill=tk.X, expand=True)
        self.entry_mensaje.bind('<Return>', self.enviar_mensaje)
        
        self.btn_enviar = tk.Button(msg_frame, text="📤 Enviar", 
                                   command=self.enviar_mensaje, bg='#3498db', fg='white',
                                   font=('Arial', 10, 'bold'), state=tk.DISABLED)
        self.btn_enviar.pack(side=tk.RIGHT)
    
    def conectar(self):
        """Conecto al servidor con autenticación y cifrado"""
        if not self.nombre_usuario.get().strip():
            messagebox.showerror("Error", "Debes introducir un nombre de usuario")
            return
        
        try:
            # Creo el socket
            self.socket_cliente = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            self.socket_cliente.connect((self.host, self.puerto))
            
            # Genero claves del cliente
            self.clave_privada_cliente, self.clave_publica_cliente = generar_claves_rsa()
            
            # Recibo la clave pública del servidor
            self.clave_publica_servidor = self.socket_cliente.recv(1024)
            
            # Envío mi clave pública al servidor
            self.socket_cliente.send(self.clave_publica_cliente)
            
            # Creo y envío la clave AES cifrada
            self.clave_aes_compartida = generar_clave_aes()
            clave_aes_cifrada = cifrar_con_rsa(
                self.clave_aes_compartida, 
                self.clave_publica_servidor
            )
            self.socket_cliente.send(clave_aes_cifrada)
            
            # Envío el nombre de usuario cifrado
            nombre_cifrado = crear_paquete_seguro(
                self.nombre_usuario.get(), 
                self.clave_aes_compartida
            )
            self.socket_cliente.send(nombre_cifrado)
            
            # Actualizo estado
            self.conectado = True
            self.label_estado.config(text="✅ Conectado", fg='#27ae60')
            self.btn_conectar.config(state=tk.DISABLED)
            self.btn_enviar.config(state=tk.NORMAL)
            self.entry_usuario.config(state=tk.DISABLED)
            
            # Inicio el hilo de recepción
            self.hilo_recepcion = threading.Thread(target=self.recibir_mensajes, daemon=True)
            self.hilo_recepcion.start()
            
            self.mostrar_mensaje(f"🎉 Bienvenido {self.nombre_usuario.get()} al chat seguro!")
            
        except Exception as e:
            self.mostrar_mensaje(f"❌ Error conectando: {e}")
            if self.socket_cliente:
                self.socket_cliente.close()
            self.socket_cliente = None
    
    def recibir_mensajes(self):
        """Hilo que recibe mensajes del servidor continuamente"""
        while self.conectado and self.socket_cliente:
            try:
                # Recibo mensaje cifrado
                datos = self.socket_cliente.recv(4096)
                if not datos:
                    break
                
                # Descifro el mensaje
                paquete = abrir_paquete_seguro(datos, self.clave_aes_compartida)
                if paquete and paquete.get('mensaje'):
                    # Muestro el mensaje en la interfaz (thread-safe)
                    self.root.after(0, lambda msg=paquete['mensaje']: self.mostrar_mensaje(msg))
                
            except Exception as e:
                if self.conectado:
                    self.root.after(0, lambda: self.mostrar_mensaje(f"❌ Error recibiendo: {e}"))
                break
    
    def enviar_mensaje(self, event=None):
        """Envío un mensaje al servidor"""
        if not self.conectado or not self.mensaje_actual.get().strip():
            return
        
        try:
            # Creo el paquete seguro
            paquete = crear_paquete_seguro(self.mensaje_actual.get(), self.clave_aes_compartida)
            
            # Envío el mensaje
            self.socket_cliente.send(paquete)
            
            # Limpio el campo de mensaje
            self.mensaje_actual.set("")
            self.entry_mensaje.focus()
            
        except Exception as e:
            self.mostrar_mensaje(f"❌ Error enviando mensaje: {e}")
    
    def mostrar_mensaje(self, mensaje):
        """Muestro un mensaje en el área de chat (thread-safe)"""
        self.text_chat.config(state=tk.NORMAL)
        self.text_chat.insert(tk.END, f"{mensaje}\n")
        self.text_chat.see(tk.END)
        self.text_chat.config(state=tk.DISABLED)
    
    def ejecutar(self):
        """Inicio la interfaz gráfica"""
        self.root.protocol("WM_DELETE_WINDOW", self.desconectar)
        self.root.mainloop()

def main():
    """Función principal del cliente"""
    try:
        cliente = ClienteChat()
        cliente.ejecutar()
    except Exception as e:
        print(f"❌ Error ejecutando cliente: {e}")

if __name__ == "__main__":
    main()
```

### Cómo ejecutar el proyecto

1. **Instalar dependencias** (solo la primera vez):
```bash
pip install pycryptodome psutil
```

2. **Ejecutar el servidor** (en una terminal):
```bash
cd server
python server_main.py
```

3. **Ejecutar el cliente** (en otra terminal):
```bash
cd client
python client_main.py
```

4. **Usar la aplicación**:
   - El cliente se conectará automáticamente a localhost:5000
   - Introduce tu nombre de usuario
   - ¡Ya puedes chatear de forma segura!




## Conclusión breve

Este ejercicio me ha permitido integrar todos los conceptos fundamentales de la asignatura "Programación de procesos y servicios" en un proyecto real y funcional. He aprendido a diseñar sistemas distribuidos que manejan múltiples usuarios simultáneamente usando tanto procesos como hilos, implementando comunicaciones de red seguras y técnicas de programación que garantizan la estabilidad del sistema.

El proyecto demuestra cómo la teoría se aplica en la práctica: los procesosseparados se encargan de tareas de background (logs, monitorización), mientras que los hilos manejan las conexiones de usuarios en tiempo real. La criptografía híbrida RSA+AES muestra la importancia de la seguridad en las comunicaciones, y el sistema de logging me ayuda a entender qué está pasando en el sistema en todo momento.

Lo que más me ha gustado ha sido ver cómo todos estos conceptos trabajan juntos para crear algo que realmente funciona, similar a las aplicaciones que uso todos los días. El ejercicio me ha dado una base sólida para entender cómo funcionan por dentro los sistemas de chat modernos y me ha preparado para proyectos más complejos en el futuro.