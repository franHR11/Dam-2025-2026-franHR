# 🛡️ ChatServer Seguro Distribuido

## 📋 Descripción del Proyecto

Este es un sistema de chat cliente-servidor seguro y distribuido que implementa múltiples tecnologías de programación avanzada. El proyecto demuestra conocimientos en programación multiproceso, multihilo, comunicaciones en red, y técnicas de programación segura.

## 🎯 Objetivos del Proyecto

- Implementar un sistema de chat funcional con múltiples clientes simultáneos
- Asegurar todas las comunicaciones con criptografía híbrida (RSA + AES)
- Utilizar programación multiproceso para el servidor
- Manejar múltiples clientes con programación multihilo
- Monitorizar procesos y recursos del sistema
- Implementar logging detallado de todas las actividades
- Proporcionar interfaz gráfica amigable para el cliente

## 🏗️ Arquitectura del Sistema

```
ChatServerSeguro/
├── server/                     # Componentes del servidor
│   ├── server_main.py         # Servidor principal con multiprocessing
│   ├── server_thread.py       # Manejo de hilos para clientes
│   ├── crypto_utils.py        # Utilidades de criptografía
│   ├── process_monitor.py     # Monitor de procesos
│   └── log_service.py         # Servicio de logging
├── client/                    # Componentes del cliente
│   ├── client_main.py         # Cliente GUI con Tkinter
│   └── client_thread.py       # Hilos de comunicación
├── config/                    # Configuración
│   └── settings.json          # Configuración del sistema
├── logs/                      # Directorio de logs
│   ├── server.log             # Logs del servidor
│   └── connections.log        # Logs de conexiones
├── requirements.txt           # Dependencias
└── README.md                  # Este archivo
```

## 🔧 Tecnologías Implementadas

### Unidad 1: Programación Multiproceso
- **Ejecutables y procesos**: El servidor se divide en múltiples procesos independientes
- **Estados de procesos**: Monitorización activa del estado de cada proceso
- **Gestión de procesos**: Control del ciclo de vida completo de los procesos
- **Sincronización**: Uso de colas para comunicación entre procesos

### Unidad 2: Programación Multihilo
- **Contexto de ejecución**: Cada cliente se maneja en un hilo separado
- **Sincronización**: Uso de Lock() para proteger secciones críticas
- **Comunicación entre hilos**: Mecanismos de paso de mensajes
- **Gestión de prioridades**: Manejo automático de prioridades

### Unidad 3: Comunicaciones en Red
- **Modelo cliente-servidor**: Arquitectura clásica con servidor central
- **Sockets TCP/IP**: Comunicación fiable y ordenada
- **Conexiones simultáneas**: Manejo de múltiples clientes concurrentes
- **Protocolos personalizados**: Protocolo de comunicación específico

### Unidad 4: Generación de Servicios en Red
- **Servicios persistententes**: El servidor funciona como un servicio continuo
- **Protocolos estándar**: Uso de TCP/IP para comunicación
- **Monitorización**: Sistema de monitoreo en tiempo real
- **Gestión de errores**: Manejo robusto de excepciones

### Unidad 5: Programación Segura
- **Criptografía asimétrica**: RSA para intercambio de claves
- **Criptografía simétrica**: AES para cifrado de mensajes
- **Protocolos seguros**: Comunicación cifrada de extremo a extremo
- **Integridad de datos**: Verificación de mensajes

### Unidad 6: Técnicas de Programación Segura
- **Validación de entradas**: Control exhaustivo de datos
- **Políticas de acceso**: Gestión de usuarios y permisos
- **Logging seguro**: Registro detallado de actividades
- **Control de errores**: Manejo de excepciones seguro

## 🚀 Instalación y Ejecución

### Prerrequisitos
- Python 3.8 o superior
- pip (gestor de paquetes de Python)

### Instalación de Dependencias
```bash
pip install -r requirements.txt
```

### Ejecución del Servidor
```bash
cd server
python server_main.py
```

### Ejecución del Cliente
```bash
cd client
python client_main.py
```

## 📊 Características Principales

### 🔐 Seguridad
- **Cifrado RSA**: Para intercambio seguro de claves
- **Cifrado AES**: Para mensajes del chat
- **Autenticación**: Verificación de clientes
- **Integridad**: Verificación de mensajes

### 🌐 Conectividad
- **Multiplataforma**: Funciona en Windows, Linux y macOS
- **Red Local**: Optimizado para comunicaciones en red local
- **Conexiones Simultáneas**: Soporte para múltiples clientes
- **Reconexión**: Manejo automático de reconexiones

### 📈 Monitorización
- **Uso de CPU**: Monitorización en tiempo real
- **Uso de Memoria**: Control de recursos
- **Procesos Activos**: Listado de procesos Python
- **Conexiones de Red**: Estado de las conexiones

### 📝 Logging
- **Logs Detallados**: Registro completo de actividades
- **Timestamps**: Marcas de tiempo precisas
- **Rotación de Logs**: Gestión automática de archivos
- **Niveles de Log**: Clasificación por importancia

## 🎮 Uso del Sistema

### Inicio Rápido
1. Inicia el servidor: `python server/server_main.py`
2. Abre múltiples clientes: `python client/client_main.py`
3. Conecta los clientes al servidor
4. Comienza a chatear de forma segura

### Comandos del Cliente
- Los mensajes se envían escribiendo y presionando Enter
- La conexión se gestiona desde la interfaz gráfica
- El estado de conexión se muestra en tiempo real

### Configuración
Edita `config/settings.json` para personalizar:
- Dirección y puerto del servidor
- Parámetros de seguridad
- Configuración de logging
- Opciones de monitorización

## 🧪 Pruebas de Funcionalidad

### Prueba de Conexión
```bash
# Terminal 1 - Servidor
python server/server_main.py

# Terminal 2 - Cliente 1
python client/client_main.py

# Terminal 3 - Cliente 2
python client/client_main.py
```

### Verificación de Seguridad
- Todos los mensajes están cifrados
- Las claves se intercambian de forma segura
- Las comunicaciones son privadas

### Monitorización del Sistema
- El monitor muestra CPU, memoria y procesos
- Los logs registran todas las actividades
- Las estadísticas se actualizan en tiempo real

## 🔍 Solución de Problemas

### Errores Comunes
1. **Conexión rechazada**: Verifica que el servidor esté activo
2. **Error de criptografía**: Instala pycryptodome correctamente
3. **Problemas de logging**: Verifica permisos del directorio logs
4. **Error de tkinter**: En Linux, instala python3-tk

### Depuración
- Habilita el modo verbose en los logs
- Revisa el monitor de procesos
- Verifica la configuración de red
- Consulta los archivos de log

## 📈 Mejoras Futuras

### Funcionalidades Adicionales
- [ ] Autenticación de usuarios con base de datos
- [ ] Salas de chat privadas
- [ ] Transferencia de archivos cifrados
- [ ] Videoconferencias seguras
- [ ] Mensajería offline

### Optimizaciones
- [ ] Balance de carga
- [ ] Caché de mensajes
- [ ] Compresión de datos
- [ ] Protocolos de retransmisión

## 📚 Referencias

### Documentación
- [Python threading](https://docs.python.org/3/library/threading.html)
- [Python multiprocessing](https://docs.python.org/3/library/multiprocessing.html)
- [Python sockets](https://docs.python.org/3/library/socket.html)
- [PyCryptodome](https://www.pycryptodome.org/)
- [Tkinter](https://docs.python.org/3/library/tkinter.html)

### Seguridad
- [RSA Algorithm](https://en.wikipedia.org/wiki/RSA_(cryptosystem))
- [AES Encryption](https://en.wikipedia.org/wiki/Advanced_Encryption_Standard)
- [Public Key Cryptography](https://en.wikipedia.org/wiki/Public-key_cryptography)

## 👥 Autores

**Desarrollado por:** Fran - Estudiante de Desarrollo de Aplicaciones Multiplataforma (DAM)

**Asignatura:** Programación de Procesos y Servicios

**Centro:** IES - 2º DAM

## 📄 Licencia

Este proyecto es educativo y se desarrolla como parte del ejercicio de "La Milla Extra" para la asignatura de Programación de Procesos y Servicios.

---

**Nota:** Este proyecto implementa conceptos avanzados de programación y seguridad. Su uso está destinado exclusivamente para fines educativos y demostrativos.