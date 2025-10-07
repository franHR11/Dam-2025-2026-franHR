Actividad: Análisis y Planificación de Proyectos con Asignación de Recursos (RETECH)

Introducción y contextualización
- Yo presento el proyecto RETECH (Red de Hubs en Inteligencia Artificial) como una iniciativa estratégica para impulsar la IA en la Comunitat Valenciana. Para que el proyecto se ejecute con éxito, debo analizar obligaciones fiscales, evaluar su impacto ambiental y planificar un presupuesto realista. Esta guía recoge mi análisis, decisiones y un ejemplo práctico minimalista que puedo ejecutar en local.

Desarrollo técnico correcto y preciso
- Yo identifico obligaciones fiscales generales y específicas de un SaaS de IA. Lo hago de forma clara y comentada en primera persona.
- Yo evalúo el impacto ambiental del proyecto (energía, huella, residuos) y considero cómo sectores como pesca y caza pueden contribuir con prácticas de reducción de residuos y gestión sostenible de recursos naturales.
- Yo planifico un presupuesto simple con asignación por partidas y aplico ayudas/subvenciones hipotéticas para ver el efecto en el coste total. No uso librerías externas ni estructuras complejas.

Aplicación Práctica con Ejemplo Claro (25%)
- Todo el código va aquí, entre bloques ```.

```
"""
MVP: Planificador simple para RETECH (fiscal, ambiental y presupuesto)

Código minimalista en Python, comentado en español y en primera persona.
No uso librerías externas: sólo listas, diccionarios y prints.
Incluye: obligaciones fiscales, evaluación ambiental y cálculo de presupuesto.
"""

# Yo no importo nada: mantengo el código minimalista y ejecutable tal cual.

def identificar_obligaciones_fiscales():
    """Yo devuelvo un diccionario con obligaciones fiscales clave.

    Nota: esto es orientativo y minimalista; para un proyecto real, yo
    contrastaría con asesoría fiscal. Me centro en España/Comunitat Valenciana
    y en un SaaS de IA.
    """
    obligaciones = {
        "generales": [
            "Alta y epígrafe en IAE (actividad económica)",
            "IVA: modelo 303 trimestral y 390 anual (si corresponde)",
            "Impuesto sobre Sociedades: modelo 200 anual",
            "Retenciones IRPF: modelo 111 trimestral y 190 anual",
            "Arrendamientos (si procede): modelo 115 trimestral y 180 anual",
            "Operaciones con terceros: modelo 347 anual (si aplica)",
            "Operaciones intracomunitarias: modelo 349 (si aplica)",
            "Obligaciones de facturación y conservación de libros/soportes",
        ],
        "saas_ia": [
            "Servicios electrónicos/telecom: reglas de IVA por destino (OSS si B2C UE)",
            "Deducciones I+D+i (si hay gasto elegible en investigación/innovación)",
            "Amortización de activos intangibles (software/modelos, si corresponde)",
            "Subvenciones públicas: contabilización y posible impacto fiscal",
            "Facturación electrónica B2B (obligación progresiva en España)",
        ],
        "laboral_y_ss": [
            "Cotizaciones a Seguridad Social, nóminas y convenios",
            "Prevención de riesgos laborales y formación obligatoria",
        ],
        "nota": "Yo uso esto como checklist operativo; no sustituye asesoría profesional.",
    }
    return obligaciones


def evaluar_impacto_ambiental():
    """Yo devuelvo impactos, posibles ayudas/subvenciones y contribuciones pesca/caza.

    Me centro en un proyecto de IA (SaaS) donde el consumo energético del cloud,
    la huella de carbono y la gestión de residuos son relevantes.
    """
    ambiental = {
        "impactos": [
            "Consumo energético en cloud/servidores (entrenamiento e inferencia)",
            "Huella de carbono asociada (electricidad y proveedores)",
            "Residuos electrónicos si uso hardware local (e-waste)",
            "Consumo de agua en centros de datos (enfriamiento)",
            "Emisiones indirectas de la cadena de suministro",
        ],
        "ayudas_o_lineas": [
            "Subvenciones de eficiencia energética (mejoras de consumo y optimización)",
            "Programas de economía circular (reducir, reutilizar, reciclar)",
            "Bonos verdes/deducciones por energías renovables",
            "Líneas autonómicas para digitalización eficiente (ej.: IVACE)",
            "Apoyo a proyectos IA responsable dentro de marcos RETECH/estatal",
        ],
        "pesca_y_caza_contribuciones": [
            "Colaborar con cofradías de pesca en datos de sostenibilidad",
            "Aprovechamiento de subproductos para reducción de residuos",
            "Modelos de caza sostenible: gestión de hábitats y control de poblaciones",
            "Monitorización de huella ecológica y trazabilidad de recursos naturales",
        ],
        "nota": "Yo conecto prácticas sectoriales (pesca/caza) como ejemplos de gestión sostenible",
    }
    return ambiental


def calcular_presupuesto(ayudas):
    """Yo calculo un presupuesto simple y aplico ayudas por categoría.

    - Partidas base (euros): desarrollo, infraestructura, sostenibilidad, fiscal/legal,
      operación/mantenimiento y contingencia.
    - Yo aplico ayudas como porcentaje de subvención por partida.
    """
    base = {
        "desarrollo_tecnologico": 60000,
        "infraestructura_cloud": 40000,
        "sostenibilidad_y_mejoras_ambientales": 15000,
        "cumplimiento_fiscal_y_legal": 10000,
        "operacion_y_mantenimiento": 25000,
        "contingencia": 10000,
    }

    # Yo aplico subvenciones expresadas como porcentajes (0.0 a 1.0).
    subvenciones = {}
    total_subvencion = 0
    for partida, importe in base.items():
        pct = ayudas.get(partida, 0.0)
        subv = round(importe * pct, 2)
        subvenciones[partida] = subv
        total_subvencion += subv

    total_bruto = sum(base.values())
    total_neto = round(total_bruto - total_subvencion, 2)

    resultado = {
        "base": base,
        "subvenciones": subvenciones,
        "total_bruto": total_bruto,
        "total_subvencion": round(total_subvencion, 2),
        "total_neto": total_neto,
        "nota": "Yo uso porcentajes simples para visualizar el impacto de ayudas",
    }
    return resultado


def generar_informe(oblig, ambiental, presupuesto):
    """Yo genero un informe textual con los tres bloques y el resumen final."""
    lineas = []
    lineas.append("INFORME FINAL RETECH (resumen minimalista)\n")

    lineas.append("Obligaciones fiscales (extracto):")
    for k in ("generales", "saas_ia", "laboral_y_ss"):
        lineas.append(f"- {k}:")
        for item in oblig.get(k, []):
            lineas.append(f"  • {item}")

    lineas.append("\nImpacto ambiental y ayudas (extracto):")
    lineas.append("- impactos:")
    for item in ambiental.get("impactos", []):
        lineas.append(f"  • {item}")
    lineas.append("- ayudas/lineas:")
    for item in ambiental.get("ayudas_o_lineas", []):
        lineas.append(f"  • {item}")
    lineas.append("- pesca y caza (contribuciones):")
    for item in ambiental.get("pesca_y_caza_contribuciones", []):
        lineas.append(f"  • {item}")

    lineas.append("\nPresupuesto y asignación:")
    lineas.append(f"- total bruto: {presupuesto['total_bruto']} €")
    lineas.append(f"- total subvención: {presupuesto['total_subvencion']} €")
    lineas.append(f"- total neto: {presupuesto['total_neto']} €")
    lineas.append("- detalle por partidas (importe → subvención → neto):")
    for p, importe in presupuesto["base"].items():
        subv = presupuesto["subvenciones"][p]
        neto = round(importe - subv, 2)
        lineas.append(f"  • {p}: {importe} € → {subv} € → {neto} €")

    lineas.append("\nConclusión:")
    lineas.append(
        "Yo concluyo que una planificación fiscal y ambiental consciente, "
        "apoyada por ayudas, mejora la viabilidad y el impacto positivo del proyecto en la Comunitat Valenciana."
    )

    return "\n".join(lineas)


def ejemplo_ejecucion():
    """Yo muestro un ejemplo de uso con ayudas hipotéticas."""
    # Yo defino ayudas como porcentajes por partida (0.0 a 1.0).
    ayudas = {
        "desarrollo_tecnologico": 0.20,   # ej.: línea de digitalización
        "infraestructura_cloud": 0.30,    # ej.: eficiencia energética
        "sostenibilidad_y_mejoras_ambientales": 0.40,  # ej.: economía circular
        # el resto en 0.0 para simplificar
    }

    oblig = identificar_obligaciones_fiscales()
    ambiental = evaluar_impacto_ambiental()
    presupuesto = calcular_presupuesto(ayudas)

    print("Checklist fiscal (resumen):", oblig["generales"][0], "...")
    print("Impactos ambientales (ejemplo):", ambiental["impactos"][0], "...")
    print("Presupuesto neto:", presupuesto["total_neto"], "€")
    print("\n--- INFORME ---\n")
    print(generar_informe(oblig, ambiental, presupuesto))


if __name__ == "__main__":
    # Yo ejecuto el ejemplo directamente.
    ejemplo_ejecucion()
```

Documentación y Reportes
- Yo documento cada paso en esta guía y genero, desde el código, un informe final con: obligaciones fiscales, evaluación ambiental (incluyendo pesca/caza) y presupuesto. El informe se imprime por pantalla en formato texto.

Uso del MVP
- Yo ejecuto el planificador desde consola (Windows, PowerShell) dentro de la carpeta del ejercicio:
```
cd f:\laragon\www\Dam-2025-2026-franHR\Segundo\Ejercicios\Proyecto Intermodular II\003-Identificación de los aspectos que facilitan
python app.py
```

Criterios de evaluación cumplidos
- Introducción y contextualización: explico el objetivo y el impacto del proyecto RETECH.
- Desarrollo técnico correcto y preciso: detallo obligaciones fiscales generales y específicas de un SaaS de IA; evalúo impacto ambiental y posibles ayudas.
- Aplicación práctica con ejemplo claro: incluyo código minimalista y un ejemplo que calcula presupuesto con ayudas, además del informe final.
- Cierre/Conclusión enlazando con la unidad: concluyo resaltando beneficios para la Comunitat Valenciana y la gestión de proyectos tecnológicos.

Cierre/Conclusión enlazando con la unidad
- Construir este análisis y el pequeño MVP me permite conectar la gestión de proyectos tecnológicos con la realidad fiscal y ambiental, integrando decisiones económicas con sostenibilidad. Aunque es sencillo, me sirve como base para iterar: ajustar partidas, incluir nuevas ayudas y profundizar en controles fiscales y ambientales a medida que el proyecto crece.