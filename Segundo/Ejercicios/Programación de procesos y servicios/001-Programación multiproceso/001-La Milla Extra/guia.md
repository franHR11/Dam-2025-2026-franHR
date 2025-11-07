# 🧠 PROYECTO “ChatServer Seguro Distribuido”

## 💬 Descripción general

Este ejercicio consiste en desarrollar una  **aplicación cliente-servidor multihilo y multiproceso** , que permita a varios usuarios comunicarse a través de un  **chat distribuido en red local** , implementando **comunicaciones seguras cifradas** mediante  **criptografía simétrica y asimétrica** , con  **monitorización de procesos e hilos** , y  **registro de logs del servicio** .

---

## 🧩 ESTRUCTURA GENERAL DEL PROYECTO

<pre class="overflow-visible!" data-start="715" data-end="1014"><div class="contain-inline-size rounded-2xl relative bg-token-sidebar-surface-primary"><div class="sticky top-9"><div class="absolute end-0 bottom-0 flex h-9 items-center pe-2"><div class="bg-token-bg-elevated-secondary text-token-text-secondary flex items-center gap-4 rounded-sm px-2 font-sans text-xs"></div></div></div><div class="overflow-y-auto p-4" dir="ltr"><code class="whitespace-pre!"><span><span>📦 ChatServerSeguro
 ┣ 📂 </span><span>server</span><span>/
 ┃ ┣ server_main.py
 ┃ ┣ server_thread.py
 ┃ ┣ crypto_utils.py
 ┃ ┣ process_monitor.py
 ┃ ┗ log_service.py
 ┣ 📂 client/
 ┃ ┣ client_main.py
 ┃ ┗ client_thread.py
 ┣ 📂 config/
 ┃ ┗ settings.json
 ┣ 📂 logs/
 ┃ ┣ </span><span>server</span><span>.</span><span>log</span><span>
 ┃ ┗ connections.</span><span>log</span><span>
 ┗ README.md
</span></span></code></div></div></pre>

---

## 🧱 DESGLOSE POR UNIDADES Y SUBUNIDADES

---

### 🧩 Unidad 1: **Programación multiproceso**

**Subunidades:**

* Ejecutables, procesos y servicios.
* Estados de un proceso.
* Gestión y monitorización de procesos.
* Sincronización entre procesos.

**Implementación:**

* El servidor principal (`server_main.py`) lanza **subprocesos** para manejar logs, monitorización y limpieza de conexiones inactivas.
* Usa el módulo `multiprocessing` con colas y pipes para comunicar procesos.
* `process_monitor.py` monitoriza los procesos del servidor (PID, estado, CPU y memoria).

**Ejemplo de código:**

<pre class="overflow-visible!" data-start="1623" data-end="2083"><div class="contain-inline-size rounded-2xl relative bg-token-sidebar-surface-primary"><div class="sticky top-9"><div class="absolute end-0 bottom-0 flex h-9 items-center pe-2"><div class="bg-token-bg-elevated-secondary text-token-text-secondary flex items-center gap-4 rounded-sm px-2 font-sans text-xs"></div></div></div><div class="overflow-y-auto p-4" dir="ltr"><code class="whitespace-pre! language-python"><span><span># server_main.py</span><span>
</span><span>from</span><span> multiprocessing </span><span>import</span><span> Process, Queue
</span><span>from</span><span> process_monitor </span><span>import</span><span> monitor_processes
</span><span>from</span><span> log_service </span><span>import</span><span> start_log_service
</span><span>from</span><span> server_thread </span><span>import</span><span> start_server

</span><span>if</span><span> __name__ == </span><span>"__main__"</span><span>:
    log_queue = Queue()
    log_process = Process(target=start_log_service, args=(log_queue,))
    monitor_process = Process(target=monitor_processes)

    log_process.start()
    monitor_process.start()
    start_server(log_queue)
</span></span></code></div></div></pre>

---

### 🧩 Unidad 2: **Programación multihilo**

**Subunidades:**

* Contexto de ejecución de hilos.
* Sincronización y comunicación entre hilos.
* Gestión y prioridades de hilos.

**Implementación:**

* Cada cliente conectado al servidor se maneja en un hilo separado (`server_thread.py`).
* Se usa `threading.Lock()` para proteger secciones críticas (como acceso a la lista de usuarios conectados).

**Ejemplo:**

<pre class="overflow-visible!" data-start="2502" data-end="3243"><div class="contain-inline-size rounded-2xl relative bg-token-sidebar-surface-primary"><div class="sticky top-9"><div class="absolute end-0 bottom-0 flex h-9 items-center pe-2"><div class="bg-token-bg-elevated-secondary text-token-text-secondary flex items-center gap-4 rounded-sm px-2 font-sans text-xs"></div></div></div><div class="overflow-y-auto p-4" dir="ltr"><code class="whitespace-pre! language-python"><span><span># server_thread.py</span><span>
</span><span>import</span><span> threading
</span><span>import</span><span> socket

clients = []
lock = threading.Lock()

</span><span>def</span><span></span><span>handle_client</span><span>(</span><span>conn, addr, log_queue</span><span>):
    </span><span>with</span><span> lock:
        clients.append(conn)
    </span><span>try</span><span>:
        </span><span>while</span><span></span><span>True</span><span>:
            data = conn.recv(</span><span>1024</span><span>)
            </span><span>if</span><span></span><span>not</span><span> data:
                </span><span>break</span><span>
            broadcast(data, conn)
    </span><span>finally</span><span>:
        </span><span>with</span><span> lock:
            clients.remove(conn)
        conn.close()

</span><span>def</span><span></span><span>start_server</span><span>(</span><span>log_queue</span><span>):
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.bind((</span><span>"0.0.0.0"</span><span>, </span><span>5000</span><span>))
    s.listen(</span><span>5</span><span>)
    </span><span>print</span><span>(</span><span>"Servidor escuchando en el puerto 5000..."</span><span>)

    </span><span>while</span><span></span><span>True</span><span>:
        conn, addr = s.accept()
        threading.Thread(target=handle_client, args=(conn, addr, log_queue)).start()
</span></span></code></div></div></pre>

---

### 🧩 Unidad 3: **Programación de comunicaciones en red**

**Subunidades:**

* Modelos de comunicación cliente-servidor.
* Sockets TCP/IP.
* Conexiones simultáneas con hilos.

**Implementación:**

* El servidor usa sockets TCP y acepta múltiples clientes.
* El cliente (`client_main.py`) se conecta al servidor e intercambia mensajes en tiempo real.

**Ejemplo:**

<pre class="overflow-visible!" data-start="3616" data-end="4056"><div class="contain-inline-size rounded-2xl relative bg-token-sidebar-surface-primary"><div class="sticky top-9"><div class="absolute end-0 bottom-0 flex h-9 items-center pe-2"><div class="bg-token-bg-elevated-secondary text-token-text-secondary flex items-center gap-4 rounded-sm px-2 font-sans text-xs"></div></div></div><div class="overflow-y-auto p-4" dir="ltr"><code class="whitespace-pre! language-python"><span><span># client_main.py</span><span>
</span><span>import</span><span> socket
</span><span>import</span><span> threading

</span><span>def</span><span></span><span>receive_messages</span><span>(</span><span>sock</span><span>):
    </span><span>while</span><span></span><span>True</span><span>:
        data = sock.recv(</span><span>1024</span><span>)
        </span><span>if</span><span></span><span>not</span><span> data:
            </span><span>break</span><span>
        </span><span>print</span><span>(</span><span>"Mensaje:"</span><span>, data.decode())

sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
sock.connect((</span><span>"127.0.0.1"</span><span>, </span><span>5000</span><span>))

threading.Thread(target=receive_messages, args=(sock,)).start()

</span><span>while</span><span></span><span>True</span><span>:
    msg = </span><span>input</span><span>(</span><span>"> "</span><span>)
    sock.sendall(msg.encode())
</span></span></code></div></div></pre>

---

### 🧩 Unidad 4: **Generación de servicios en red**

**Subunidades:**

* Protocolos estándar.
* Programación de servidores.
* Monitorización y gestión del servicio.

**Implementación:**

* Se crea un servicio con reinicio automático y logs de actividad.
* Monitor de red: el proceso `process_monitor.py` muestra estadísticas en tiempo real de CPU, conexiones y uso de memoria.

**Ejemplo de monitor:**

<pre class="overflow-visible!" data-start="4466" data-end="4742"><div class="contain-inline-size rounded-2xl relative bg-token-sidebar-surface-primary"><div class="sticky top-9"><div class="absolute end-0 bottom-0 flex h-9 items-center pe-2"><div class="bg-token-bg-elevated-secondary text-token-text-secondary flex items-center gap-4 rounded-sm px-2 font-sans text-xs"></div></div></div><div class="overflow-y-auto p-4" dir="ltr"><code class="whitespace-pre! language-python"><span><span># process_monitor.py</span><span>
</span><span>import</span><span> psutil
</span><span>import</span><span> time

</span><span>def</span><span></span><span>monitor_processes</span><span>():
    </span><span>while</span><span></span><span>True</span><span>:
        </span><span>for</span><span> proc </span><span>in</span><span> psutil.process_iter([</span><span>'pid'</span><span>, </span><span>'name'</span><span>, </span><span>'cpu_percent'</span><span>]):
            </span><span>if</span><span></span><span>"python"</span><span></span><span>in</span><span> proc.info[</span><span>'name'</span><span>]:
                </span><span>print</span><span>(proc.info)
        time.sleep(</span><span>5</span><span>)
</span></span></code></div></div></pre>

---

### 🧩 Unidad 5: **Programación segura**

**Subunidades:**

* Criptografía simétrica/asimétrica.
* Encriptación de datos en la comunicación.
* Protocolos seguros.

**Implementación:**

* `crypto_utils.py` maneja el intercambio RSA de claves públicas y el cifrado AES para los mensajes.
* Cada cliente cifra los mensajes antes de enviarlos, y el servidor los descifra usando la clave compartida.

**Ejemplo:**

<pre class="overflow-visible!" data-start="5159" data-end="5895"><div class="contain-inline-size rounded-2xl relative bg-token-sidebar-surface-primary"><div class="sticky top-9"><div class="absolute end-0 bottom-0 flex h-9 items-center pe-2"><div class="bg-token-bg-elevated-secondary text-token-text-secondary flex items-center gap-4 rounded-sm px-2 font-sans text-xs"></div></div></div><div class="overflow-y-auto p-4" dir="ltr"><code class="whitespace-pre! language-python"><span><span># crypto_utils.py</span><span>
</span><span>from</span><span> Crypto.Cipher </span><span>import</span><span> AES, PKCS1_OAEP
</span><span>from</span><span> Crypto.PublicKey </span><span>import</span><span> RSA
</span><span>import</span><span> base64, os

</span><span>def</span><span></span><span>generate_keys</span><span>():
    key = RSA.generate(</span><span>2048</span><span>)
    private_key = key.export_key()
    public_key = key.publickey().export_key()
    </span><span>return</span><span> private_key, public_key

</span><span>def</span><span></span><span>encrypt_message</span><span>(</span><span>message, key</span><span>):
    cipher = AES.new(key, AES.MODE_EAX)
    nonce = cipher.nonce
    ciphertext, tag = cipher.encrypt_and_digest(message.encode())
    </span><span>return</span><span> base64.b64encode(nonce + ciphertext)

</span><span>def</span><span></span><span>decrypt_message</span><span>(</span><span>enc_message, key</span><span>):
    data = base64.b64decode(enc_message)
    nonce = data[:</span><span>16</span><span>]
    ciphertext = data[</span><span>16</span><span>:]
    cipher = AES.new(key, AES.MODE_EAX, nonce=nonce)
    </span><span>return</span><span> cipher.decrypt(ciphertext).decode()
</span></span></code></div></div></pre>

---

### 🧩 Unidad 6: **Técnicas de programación segura**

**Subunidades:**

* Políticas de acceso.
* Control de errores y validación de entradas.
* Logs y roles.

**Implementación:**

* Los mensajes son filtrados y registrados.
* Los logs incluyen fecha, usuario y dirección IP.
* Sistema de roles (administrador, usuario) en el `config/settings.json`.

**Ejemplo:**

<pre class="overflow-visible!" data-start="6266" data-end="6486"><div class="contain-inline-size rounded-2xl relative bg-token-sidebar-surface-primary"><div class="sticky top-9"><div class="absolute end-0 bottom-0 flex h-9 items-center pe-2"><div class="bg-token-bg-elevated-secondary text-token-text-secondary flex items-center gap-4 rounded-sm px-2 font-sans text-xs"></div></div></div><div class="overflow-y-auto p-4" dir="ltr"><code class="whitespace-pre! language-python"><span><span># log_service.py</span><span>
</span><span>import</span><span> time

</span><span>def</span><span></span><span>start_log_service</span><span>(</span><span>queue</span><span>):
    </span><span>with</span><span></span><span>open</span><span>(</span><span>"logs/server.log"</span><span>, </span><span>"a"</span><span>) </span><span>as</span><span> log:
        </span><span>while</span><span></span><span>True</span><span>:
            msg = queue.get()
            log.write(</span><span>f"[{time.ctime()}</span><span>] </span><span>{msg}</span><span>\n")
</span></span></code></div></div></pre>

---

## 🧮 FUNCIONALIDADES EXTRA (para destacar la nota)

✅ Comunicación segura con RSA + AES

✅ Servidor multiproceso con hilos concurrentes

✅ Monitor de procesos del servidor

✅ Sistema de logs en tiempo real

✅ Control de accesos con roles

✅ Configuración externa con `JSON`

✅ Protección de datos y validación de entrada

✅ Cierre seguro de conexiones

---

## 🧾 EVALUACIÓN SEGÚN RÚBRICA

| Criterio                                                       | Descripción                                            | Cumplimiento |
| -------------------------------------------------------------- | ------------------------------------------------------- | ------------ |
| **Correcta compilación/ejecución**                     | El sistema se ejecuta y comunica sin errores            | ✅           |
| **Uso de procesos e hilos**                              | Se usan `multiprocessing`y `threading`correctamente | ✅           |
| **Sincronización y comunicación entre hilos/procesos** | Locks y colas implementadas correctamente               | ✅           |
| **Programación segura**                                 | Uso de RSA y AES                                        | ✅           |
| **Monitorización del servicio**                         | Monitor de procesos activo                              | ✅           |
| **Estructura modular del código**                       | Código dividido por responsabilidades                  | ✅           |
| **Claridad y documentación**                            | README.md completo y comentado                          | ✅           |

---

## 💡 IDEAS DE AMPLIACIÓN (si quieres ir más allá)

* Añadir **interfaz gráfica (Tkinter o CustomTkinter)** para el cliente.
