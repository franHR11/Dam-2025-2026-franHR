class AgenteIAValencia:
    def __init__(self):
        self.base_conocimiento = {
            "horario": "Nuestro horario de atención es de lunes a viernes de 9:00 a 18:00.",
            "envios": "Realizamos envíos a toda la Comunidad Valenciana en 24-48 horas.",
            "devoluciones": "Las devoluciones se aceptan en un plazo de 14 días con ticket de compra.",
            "facturacion": "La facturación se realiza mensualmente y se envía por correo electrónico.",
            "localizacion": "Estamos ubicados en Valencia, Comunidad Valenciana, cumpliendo con todas las normativas locales."
        }
        self.incidencias = []
        self.clientes_registrados = []
        
    def responder_pregunta(self, pregunta):
        pregunta_lower = pregunta.lower()
        for clave, respuesta in self.base_conocimiento.items():
            if clave in pregunta_lower:
                return respuesta
        return "Lo siento, no tengo información sobre esa consulta. ¿Puedes reformular la pregunta?"
    
    def registrar_incidencia(self, cliente, descripcion):
        incidencia = {
            "id": len(self.incidencias) + 1,
            "cliente": cliente,
            "descripcion": descripcion,
            "estado": "pendiente",
            "ubicacion_datos": "Valencia, Comunidad Valenciana"
        }
        self.incidencias.append(incidencia)
        return f"Incidencia registrada con ID: {incidencia['id']}. Nos pondremos en contacto pronto."
    
    def registrar_cliente(self, nombre, email, localidad):
        if "valencia" in localidad.lower() or "alicante" in localidad.lower() or "castellon" in localidad.lower():
            cliente = {
                "id": len(self.clientes_registrados) + 1,
                "nombre": nombre,
                "email": email,
                "localidad": localidad,
                "datos_almacenados": "Localmente en Valencia"
            }
            self.clientes_registrados.append(cliente)
            return f"Cliente {nombre} registrado correctamente en nuestra base de datos local."
        else:
            return "Lo sentimos, actualmente solo servimos a clientes de la Comunidad Valenciana."
    
    def consultar_incidencia(self, id_incidencia):
        for incidencia in self.incidencias:
            if incidencia["id"] == id_incidencia:
                return f"Incidencia {id_incidencia}: {incidencia['descripcion']}. Estado: {incidencia['estado']}."
        return "No se encontró ninguna incidencia con ese ID."
    
    def actualizar_incidencia(self, id_incidencia, nuevo_estado):
        for incidencia in self.incidencias:
            if incidencia["id"] == id_incidencia:
                incidencia["estado"] = nuevo_estado
                return f"Incidencia {id_incidencia} actualizada a estado: {nuevo_estado}."
        return "No se encontró ninguna incidencia con ese ID."
    
    def mostrar_info_competitividad(self):
        return """
        Nuestro agente de IA mejora la competitividad empresarial valenciana mediante:
        - Respuestas inmediatas 24/7 a consultas frecuentes
        - Gestión eficiente de incidencias
        - Almacenamiento local de datos cumpliendo normativas
        - Soporte personalizado para empresas valencianas
        - Reducción de costos operativos
        """

# Función principal para demostrar el funcionamiento del agente
def main():
    agente = AgenteIAValencia()
    
    print("=== AGENTE DE IA PARA ATENCIÓN AL CLIENTE - COMUNIDAD VALENCIANA ===")
    print("Bienvenido al sistema de atención automatizado")
    print(agente.mostrar_info_competitividad())
    print("\n--- DEMOSTRACIÓN DEL SISTEMA ---")
    
    # Registro de clientes
    print("\n1. Registro de clientes:")
    print(agente.registrar_cliente("María García", "maria@empresa-valenciana.es", "Valencia"))
    print(agente.registrar_cliente("Juan López", "juan@comercio-alicante.com", "Alicante"))
    print(agente.registrar_cliente("Empresa Madrid", "contacto@madrid.es", "Madrid"))
    
    # Respuesta a preguntas frecuentes
    print("\n2. Respuestas a preguntas frecuentes:")
    preguntas = ["¿Cuál es el horario de atención?", "¿Hacen envíos a Castellón?", 
                 "¿Cuál es la política de devoluciones?", "¿Dónde están ubicados?"]
    
    for pregunta in preguntas:
        print(f"Cliente: {pregunta}")
        print(f"Agente IA: {agente.responder_pregunta(pregunta)}\n")
    
    # Gestión de incidencias
    print("\n3. Gestión de incidencias:")
    incidencia1 = agente.registrar_incidencia("María García", "Producto recibido con defecto")
    print(incidencia1)
    incidencia2 = agente.registrar_incidencia("Juan López", "Consulta sobre facturación")
    print(incidencia2)
    
    # Consulta y actualización de incidencias
    print("\n4. Consulta y actualización de incidencias:")
    print(agente.consultar_incidencia(1))
    print(agente.actualizar_incidencia(1, "en proceso"))
    print(agente.consultar_incidencia(1))
    
    print("\n=== FIN DE DEMOSTRACIÓN ===")

if __name__ == "__main__":
    main()