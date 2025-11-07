# 🚀 ChatServer Seguro - Guía Rápida de Instalación y Uso

## 📋 Requisitos Previos

- **Python 3.8+** instalado en el sistema
- **pip** (gestor de paquetes de Python)
- **20MB** de espacio libre en disco

## 🔧 Instalación Paso a Paso

### 1. Verificar Python
```bash
python --version
# Debe mostrar Python 3.8 o superior
```

### 2. Instalar Dependencias
```bash
pip install -r requirements.txt
```

### 3. Verificar Instalación
```bash
python test_system.py
# Debe mostrar: "¡Todas las pruebas pasaron!"
```

## 🎮 Uso Rápido

### Iniciar el Servidor
```bash
python run_server.py
```
✅ El servidor se iniciará en `0.0.0.0:5000`

### Iniciar Clientes
Abre **nuevas terminales** para cada cliente:
```bash
python run_client.py
```

## 📱 Uso del Cliente (Interfaz Gráfica)

1. **Conectar**: Ingresa `127.0.0.1` y puerto `5000`
2. **Chatear**: Escribe mensajes y presiona Enter
3. **Desconectar**: Haz clic en "Desconectar"

## 🔍 Características del Sistema

### 🔐 Seguridad
- **RSA 2048 bits**: Intercambio seguro de claves
- **AES 256 bits**: Cifrado de mensajes
- **Comunicación cifrada**: De extremo a extremo

### 🌐 Red
- **Multi-cliente**: Soporte para múltiples usuarios
- **TCP/IP**: Comunicación fiable
- **Tiempo real**: Mensajes instantáneos

### 📊 Monitorización
- **CPU y memoria**: Uso en tiempo real
- **Procesos Python**: Listado activo
- **Logs detallados**: Registro completo

## 🛠️ Configuración

Edita `config/settings.json` para personalizar:
- Dirección y puerto del servidor
- Parámetros de seguridad
- Configuración de logging

## 📁 Estructura del Proyecto

```
ChatServerSeguro/
├── server/          # Componentes del servidor
├── client/          # Cliente con interfaz gráfica
├── config/          # Configuración JSON
├── logs/            # Logs del sistema
└── *.py            # Scripts de ejecución
```

## 🧪 Pruebas Diagnósticas

### Probar Criptografía
```bash
cd server && python crypto_utils.py
```

### Probar Monitor
```bash
cd server && python process_monitor.py
```

### Probar Sistema Completo
```bash
python test_system.py
```

## ❓ Solución de Problemas

### Error: "No module named 'pycryptodome'"
```bash
pip install pycryptodome
```

### Error: "No module named 'psutil'"
```bash
pip install psutil
```

### Error: "tkinter no disponible" (Linux)
```bash
sudo apt-get install python3-tk
```

### Error: "Conexión rechazada"
- Asegúrate que el servidor esté activo
- Verifica el puerto (default: 5000)

## 📚 Tecnologías Implementadas

### Unidad 1: Multiproceso
- Procesos independientes para logging y monitoreo
- Comunicación entre procesos con colas
- Gestión de ciclo de vida de procesos

### Unidad 2: Multihilo
- Cada cliente en un hilo separado
- Sincronización con threading.Lock()
- Comunicación bidireccional

### Unidad 3: Comunicaciones en Red
- Sockets TCP/IP
- Modelo cliente-servidor
- Conexiones simultáneas

### Unidad 4: Servicios en Red
- Servicio persistente
- Monitorización en tiempo real
- Logs detallados

### Unidad 5: Programación Segura
- Criptografía asimétrica (RSA)
- Criptografía simétrica (AES)
- Comunicación cifrada

### Unidad 6: Técnicas de Seguridad
- Validación de entradas
- Control de errores
- Logging seguro

## 🎯 Ejemplo de Uso

### Terminal 1 - Servidor
```bash
$ python run_server.py
🚀 Iniciando ChatServer Seguro...
✅ Dependencias verificadas
📂 Cambiado al directorio del servidor
🔧 Iniciando servidor principal...
📝 Los logs se guardarán en ../logs/
Servidor iniciado en 0.0.0.0:5000
```

### Terminal 2 - Cliente 1
```bash
$ python run_client.py
🚀 Iniciando Cliente de Chat Seguro...
✅ Dependencias verificadas
✅ Tkinter disponible
📂 Cambiado al directorio del cliente
🖥️ Iniciando interfaz gráfica del cliente...
```

### Terminal 3 - Cliente 2
```bash
$ python run_client.py
```

## 📞 Soporte

### Consulta Rápida
- `python test_system.py` - Verificación completa
- `python --version` - Versión de Python
- `pip list` - Paquetes instalados

### Logs del Sistema
- `logs/server.log` - Actividad del servidor
- Monitor en tiempo real - CPU y memoria

---

**✨ ¡Listo para usar!** El sistema está completamente configurado y seguro.

**🎓 Proyecto desarrollado para:** Programación de Procesos y Servicios - 2º DAM