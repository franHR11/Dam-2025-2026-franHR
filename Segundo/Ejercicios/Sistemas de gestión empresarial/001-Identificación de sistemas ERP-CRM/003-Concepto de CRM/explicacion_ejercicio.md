# Ejercicio: Diseño de un Sistema CRM Simple para un Pueblo Costero

## Introducción y contextualización (25%)

Hola, soy un estudiante de DAM y estoy trabajando en este proyecto. Un CRM, o Customer Relationship Management, es un sistema que me ayuda a gestionar las relaciones con mis clientes. Básicamente, es como una herramienta que me permite recordar qué les gusta a mis clientes, qué compran y cómo puedo hacer que vuelvan más veces. En mi pueblo costero, donde me encanta pescar, quiero usar este CRM para mejorar cómo trato a los clientes locales. Por ejemplo, si un cliente compra mucho equipo de pesca, puedo ofrecerle consejos sobre dónde pescar mejor o productos relacionados. El CRM me ayudará a analizar sus hábitos de compra para personalizar ofertas, como descuentos en cañas de pescar o invitaciones a eventos de pesca local. Esto no solo mejora las ventas, sino que también fortalece la comunidad, ya que muchos clientes son pescadores como yo.

## Desarrollo técnico correcto y preciso (25%)

Para este ejercicio, he diseñado un sistema CRM simple usando solo listas y diccionarios en Python, sin librerías externas. Primero, creo una lista de clientes. Cada cliente es un diccionario con su nombre, una lista de productos que ha comprado y la frecuencia de compra (cuántas veces ha venido).

Aquí va la estructura de datos que uso:

- Una lista llamada `clientes` que contiene diccionarios.
- Cada diccionario tiene:
  - 'nombre': el nombre del cliente (string).
  - 'productos': una lista de productos comprados (listas de strings).
  - 'frecuencia': un número que indica cuántas veces ha comprado (int).

Para recopilar datos, simplemente agrego manualmente algunos clientes de ejemplo, como pescadores locales que compran cañas, anzuelos, etc.

Luego, para el análisis, recorro la lista y cuento cosas como:
- Los productos más comprados en total.
- Los clientes con mayor frecuencia de compra.
- Tendencias, como si muchos compran productos de pesca.

Esto me da insights para personalizar ofertas. Por ejemplo, si veo que "cañas de pescar" es lo más comprado, puedo sugerir accesorios relacionados.

## Aplicación práctica con ejemplo claro (25%)

Aquí está todo el código de mi aplicación CRM simple. Lo he escrito en Python de manera minimalista, usando solo listas y bucles básicos. Los comentarios están en primera persona, como si yo estuviera explicando lo que hago mientras programo.

```python
# Hola, soy yo programando este CRM simple. Primero, creo una lista vacía para mis clientes.
clientes = []

# Ahora, agrego algunos clientes de ejemplo. Yo vivo en un pueblo costero, así que incluyo productos relacionados con la pesca, mi hobby.
# Agrego el primer cliente: un pescador local llamado Juan.
cliente1 = {
    'nombre': 'Juan',
    'productos': ['caña de pescar', 'anzuelos', 'sedal'],
    'frecuencia': 5
}
clientes.append(cliente1)

# Agrego otro cliente: María, que compra menos pero cosas variadas.
cliente2 = {
    'nombre': 'María',
    'productos': ['caña de pescar', 'botas de pesca', 'anzuelos'],
    'frecuencia': 3
}
clientes.append(cliente2)

# Y uno más: Pedro, un cliente frecuente.
cliente3 = {
    'nombre': 'Pedro',
    'productos': ['sedal', 'anzuelos', 'caña de pescar', 'botas de pesca'],
    'frecuencia': 7
}
clientes.append(cliente3)

# Ahora, voy a analizar los datos. Primero, cuento los productos más comprados.
# Creo un diccionario para contar cada producto.
conteo_productos = {}
for cliente in clientes:
    for producto in cliente['productos']:
        if producto in conteo_productos:
            conteo_productos[producto] += 1
        else:
            conteo_productos[producto] = 1

# Encuentro el producto más comprado.
producto_mas_comprado = max(conteo_productos, key=conteo_productos.get)
print(f"El producto más comprado es: {producto_mas_comprado} (comprado {conteo_productos[producto_mas_comprado]} veces)")

# Ahora, encuentro el cliente con mayor frecuencia de compra.
cliente_mas_frecuente = max(clientes, key=lambda c: c['frecuencia'])
print(f"El cliente más frecuente es: {cliente_mas_frecuente['nombre']} (ha comprado {cliente_mas_frecuente['frecuencia']} veces)")

# Para tendencias, veo si hay muchos productos de pesca. Cuento cuántos productos contienen "pesca".
productos_pesca = 0
for cliente in clientes:
    for producto in cliente['productos']:
        if 'pesca' in producto.lower():
            productos_pesca += 1
print(f"Tendencia: Hay {productos_pesca} productos relacionados con la pesca comprados en total.")

# Finalmente, genero un informe simple.
print("\n--- Informe de Análisis CRM ---")
print("Clientes registrados:")
for cliente in clientes:
    print(f"- {cliente['nombre']}: productos {cliente['productos']}, frecuencia {cliente['frecuencia']}")
print(f"Producto estrella: {producto_mas_comprado}")
print(f"Cliente VIP: {cliente_mas_frecuente['nombre']}")
print("Con esta info, puedo ofrecer ofertas personalizadas, como descuentos en cañas para Juan o invitaciones a pesca para Pedro.")
```

Este código es simple y corre en cualquier Python básico. Primero define los clientes, luego analiza y muestra resultados. Lo ejecuto y veo el informe en la consola.

## Cierre/Conclusión enlazando con la unidad (25%)

En conclusión, este sistema CRM simple me ha permitido analizar el comportamiento de compra de mis clientes en el pueblo costero. He identificado que productos como las cañas de pescar son populares, y clientes como Pedro compran mucho. Esto me ayuda a mejorar las relaciones ofreciendo productos personalizados, como equipo de pesca de mejor calidad o eventos locales. Como estudiante de DAM, veo cómo esto se relaciona con los sistemas ERP-CRM que estudiamos: es una base para gestionar datos empresariales de forma eficiente. En el futuro, podría expandirlo con bases de datos reales, pero por ahora, con listas básicas, ya veo el potencial para beneficiar a la organización y a la comunidad de pescadores.

---

## Criterios de evaluación

- **Introducción y contextualización (25%)**: El alumnado debe explicar claramente qué es un CRM y cómo se aplicará en su proyecto.
- **Desarrollo técnico correcto y preciso (25%)**: El alumnado debe presentar una lista detallada de clientes y sus hábitos de compra, así como un análisis exhaustivo.
- **Aplicación práctica con ejemplo claro (25%)**: El alumnado debe crear un informe que muestre cómo la información recopilada puede ser utilizada para mejorar las relaciones con los clientes.
- **Cierre/Conclusión enlazando con la unidad (25%)**: El alumnado debe concluir su trabajo, enfatizando el impacto potencial de su sistema CRM y cómo puede beneficiar a la organización.