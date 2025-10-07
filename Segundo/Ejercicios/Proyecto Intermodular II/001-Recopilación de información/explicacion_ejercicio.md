# Análisis de una empresa de inteligencia artificial en Valencia  

## Introducción y contextualización  

En este análisis selecciono a **Tyris AI**, una empresa de inteligencia artificial ubicada en Valencia (España). La he elegido porque ofrece soluciones de **visión artificial** y **analítica predictiva** orientadas a la **Industria 4.0**, con proyectos en sectores clave como la automoción, la gestión del agua y la metalurgia.  

El objetivo de este trabajo es comprender su estructura organizativa y detallar las funciones de cada uno de sus departamentos, aplicando una metodología práctica de búsqueda (“programar”) y de toma de notas (“pesca”), tal como se plantea en la actividad intermodular.  

Fuentes consultadas:  
- Sitio web oficial: [https://tyris.ai/](https://tyris.ai/)  
- Directorio Startup Valencia: [https://startupvalencia.org/directory/tyris-ai/](https://startupvalencia.org/directory/tyris-ai/)  
- Artículo en prensa local (Valencia Plaza): [https://valenciaplaza.com/tyris-sofware-inteligencia-artificial-innovacion-abierta](https://valenciaplaza.com/tyris-sofware-inteligencia-artificial-innovacion-abierta)  

---

## Estructura organizativa  

La estructura organizativa de Tyris AI puede representarse de forma simplificada de la siguiente manera:  

- **Dirección y Administración**: define la estrategia y gestiona los recursos.  
- **I+D (Investigación y Desarrollo)**: experimenta con nuevas técnicas de IA.  
- **Desarrollo (Ingeniería de Software/ML)**: implementa modelos y aplicaciones.  
- **Ventas y Marketing**: gestiona clientes, comunicación y posicionamiento.  
- **Soporte Técnico y Operaciones**: mantiene en producción las soluciones de IA.  
- **Proyectos y Consultoría**: traduce las necesidades del cliente a entregables.  
- **Recursos Humanos**: gestiona el talento y fomenta la formación.  
- **Finanzas**: asegura la sostenibilidad económica de la empresa.  

---

## Funciones de cada departamento  

- **Dirección y Administración**: defino los objetivos estratégicos, priorizo líneas de innovación y superviso la actividad de la empresa.  
- **I+D**: experimento con algoritmos de visión artificial y analítica predictiva, documentando hallazgos para convertirlos en prototipos.  
- **Desarrollo**: transformo los prototipos en software estable y escalable, integrando modelos en los sistemas de los clientes.  
- **Ventas y Marketing**: preparo demostraciones y comunico el valor de las soluciones a potenciales clientes del sector industrial.  
- **Soporte y Operaciones**: monitorizo el rendimiento de los sistemas en producción, gestiono incidencias y aplico mejoras continuas.  
- **Proyectos y Consultoría**: organizo la planificación por sprints, recojo requisitos y los convierto en entregables técnicos.  
- **Recursos Humanos**: gestiono procesos de selección, planes de formación y programas de bienestar laboral.  
- **Finanzas**: controlo presupuestos, costes de infraestructura y márgenes de rentabilidad de cada proyecto.  

---

## Aplicación práctica con ejemplo  

Para ilustrar la metodología, he desarrollado un ejemplo de **mini-aplicación en Python** que permite:  
1. Obtener el título del sitio web de la empresa (simulando la “programación” de la búsqueda).  
2. Guardar notas en un archivo (simulando la “pesca” o registro de observaciones).  

```python
import requests
from bs4 import BeautifulSoup

# Programar: obtener título de la web
url = "https://tyris.ai/"
response = requests.get(url)
soup = BeautifulSoup(response.text, "html.parser")
titulo_web = soup.title.string.strip()

# Pesca: guardar notas
with open("notas_tyris_ai.txt", "w", encoding="utf-8") as f:
    f.write("Título de la web: " + titulo_web + "\n")
    f.write("Empresa: Tyris AI\n")
    f.write("Áreas: Visión Artificial, Analítica Predictiva, Industria 4.0\n")

print("Notas guardadas en 'notas_tyris_ai.txt'")
```

Este ejemplo muestra cómo aplicar de manera sencilla el enfoque práctico: automatizar la recogida de información y mantener un registro organizado.  

---

## Evaluación del análisis  

- **¿Información encontrada?** Sí, se ha localizado información pública suficiente para contextualizar la empresa y sus áreas principales.  
- **¿Qué falta?** Detalles internos como organigramas reales, roles específicos (por ejemplo, equipos de MLOps), métricas de rendimiento o procesos de innovación.  
- **Mejoras futuras:** realizar entrevistas, consultar documentos técnicos o casos de uso más detallados, e incluir análisis de métricas de impacto.  

---

## Conclusión  

Con esta actividad he logrado aplicar de forma práctica dos técnicas:  
- **Programar** para acceder a información pública mediante una pequeña automatización.  
- **Pesca** para registrar notas y organizar la información obtenida.  

El análisis ha permitido describir una estructura organizativa clara de Tyris AI, con ejemplos de funciones en cada departamento. De este modo, se vincula la teoría de la unidad con una aplicación práctica real, dejando abierta la posibilidad de mejorar el análisis con entrevistas o datos internos en el futuro.  

---

## Referencias  

- [Tyris AI – Inteligencia Artificial y Análisis Predictivo para Industria](https://tyris.ai/)  
- [Tyris AI en Startup Valencia](https://startupvalencia.org/directory/tyris-ai/)  
- [Artículo en Valencia Plaza sobre Tyris AI](https://valenciaplaza.com/tyris-sofware-inteligencia-artificial-innovacion-abierta)  
