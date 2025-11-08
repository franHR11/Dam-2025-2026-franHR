"""
MVP: Clasificador automático de facturas y tickets (Document AI sencillo)

Código minimalista en Python, comentado en español y en primera persona.
Cumple con los apartados: entorno, selección de modelo/API, función
classify_document, bucle principal y pruebas unitarias (en carpeta tests).
"""

from pathlib import Path
from typing import Optional

# Intento usar un modelo de NLP pre-entrenado de Hugging Face para clasificación
# zero-shot. Si no está instalado o falla, haré un clasificador por reglas simple.
try:
    from transformers import pipeline  # type: ignore
    _HAVE_TRANSFORMERS = True
except Exception:
    _HAVE_TRANSFORMERS = False

# Intento soportar imágenes mediante OCR. Para mantenerlo simple, uso Pillow y
# pytesseract si están disponibles. En Windows, Tesseract necesita instalación
# externa. Si no está, me quedaré en modo texto.
try:
    from PIL import Image  # type: ignore
    import pytesseract  # type: ignore
    _HAVE_OCR = True
except Exception:
    _HAVE_OCR = False


def _leer_texto_desde_archivo(ruta: Path) -> str:
    """Leo texto plano de un .txt de forma sencilla.

    Aquí asumo que el documento de texto es UTF-8; si no, intento latin-1.
    """
    try:
        return ruta.read_text(encoding="utf-8")
    except UnicodeDecodeError:
        return ruta.read_text(encoding="latin-1")


def _extraer_texto_de_imagen(ruta: Path) -> Optional[str]:
    """Extraigo texto de una imagen con OCR si tengo las dependencias.

    Si no tengo OCR disponible, devuelvo None y el flujo seguirá por reglas.
    """
    if not _HAVE_OCR:
        return None
    try:
        imagen = Image.open(ruta)
        # Uso pytesseract para extraer texto de la imagen.
        texto = pytesseract.image_to_string(imagen, lang="spa")
        return texto
    except Exception:
        # Si algo falla (por ejemplo, falta binario de Tesseract), regreso None.
        return None


def _clasificar_por_reglas(texto: str) -> str:
    """Clasificador muy simple por reglas/keywords.

    Aquí, como MVP minimalista, me baso en palabras típicas de facturas y tickets.
    """
    texto_l = texto.lower()

    # Palabras comunes en facturas
    factura_keywords = [
        "factura",
        "nº factura",
        "num factura",
        "cif",
        "importe total",
        "base imponible",
        "subtotal",
        "cliente",
        "proveedor",
        "fecha de emisión",
    ]

    # Palabras comunes en tickets
    ticket_keywords = [
        "ticket",
        "tpv",
        "cajero",
        "iva incluido",
        "tienda",
        "artículos",
        "unidad",
        "cambio",
        "efectivo",
    ]

    factura_hits = sum(1 for k in factura_keywords if k in texto_l)
    ticket_hits = sum(1 for k in ticket_keywords if k in texto_l)

    if factura_hits == 0 and ticket_hits == 0:
        return "desconocido"
    return "factura" if factura_hits >= ticket_hits else "ticket"


def classify_document(archivo: str) -> str:
    """Clasifico un documento simple como 'factura', 'ticket' o 'desconocido'.

    - Si recibo texto (.txt), lo analizo directamente.
    - Si recibo una imagen (jpg, jpeg, png, bmp, tiff), intento OCR.
    - Si tengo Transformers, hago zero-shot con labels ['factura', 'ticket'].
      Si falla o no está, uso reglas por palabras clave.
    """
    ruta = Path(archivo)
    if not ruta.exists():
        raise FileNotFoundError(f"No encuentro el archivo: {archivo}")

    ext = ruta.suffix.lower()
    es_texto = ext in {".txt"}
    es_imagen = ext in {".jpg", ".jpeg", ".png", ".bmp", ".tiff"}

    if not (es_texto or es_imagen):
        # Para mantenerlo minimalista, solo acepto .txt e imágenes comunes.
        return "desconocido"

    # Obtengo el texto a clasificar.
    if es_texto:
        texto = _leer_texto_desde_archivo(ruta)
    else:
        texto = _extraer_texto_de_imagen(ruta) or ""

    # Si no tengo texto (por ejemplo, OCR falló), me voy a reglas vacías.
    if not texto.strip():
        return "desconocido"

    # Intento zero-shot si tengo transformers.
    if _HAVE_TRANSFORMERS:
        try:
            clasificador = pipeline("zero-shot-classification", model="facebook/bart-large-mnli")
            resultado = clasificador(
                texto,
                candidate_labels=["factura", "ticket"],
                multi_label=False,
            )
            # Me quedo con la etiqueta con mayor score.
            etiqueta = resultado["labels"][0].lower()
            if etiqueta in {"factura", "ticket"}:
                return etiqueta
        except Exception:
            # Si falla el modelo, sigo con reglas.
            pass

    # Fallback: clasificador por reglas.
    return _clasificar_por_reglas(texto)


def _es_imagen_path(ruta: Path) -> bool:
    return ruta.suffix.lower() in {".jpg", ".jpeg", ".png", ".bmp", ".tiff"}


def main() -> None:
    """Bucle principal muy sencillo para probar el MVP.

    Yo pido rutas de archivos al usuario y muestro la clasificación.
    Para salir, el usuario escribe 'salir'.
    """
    print("Clasificador de documentos (factura/ticket). Escribe 'salir' para terminar.")
    while True:
        ruta_str = input("Ruta del archivo (.txt o imagen): ").strip()
        if ruta_str.lower() == "salir":
            print("Hasta luego.")
            break
        if not ruta_str:
            continue
        try:
            categoria = classify_document(ruta_str)
            tipo_archivo = "imagen" if _es_imagen_path(Path(ruta_str)) else "texto"
            print(f"Archivo de {tipo_archivo}. Categoría detectada: {categoria}\n")
        except FileNotFoundError as e:
            print(f"Error: {e}\n")


if __name__ == "__main__":
    main()