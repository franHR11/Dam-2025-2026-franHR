import pytest
from pathlib import Path
import importlib.util


def _load_app_module():
    """Cargo app.py como módulo, evitando problemas de import por espacios/raros."""
    base = Path(__file__).resolve().parent.parent
    app_py = base / "app.py"
    spec = importlib.util.spec_from_file_location("app_module", app_py)
    module = importlib.util.module_from_spec(spec)  # type: ignore
    assert spec and spec.loader
    spec.loader.exec_module(module)  # type: ignore
    return module


def _samples_dir() -> Path:
    # Carpeta de samples relativa a este test
    return Path(__file__).resolve().parent.parent / "samples"


def test_clasifica_factura_sample():
    app = _load_app_module()
    ruta = _samples_dir() / "factura.txt"
    assert ruta.exists()
    categoria = app.classify_document(str(ruta))
    assert categoria == "factura"


def test_clasifica_ticket_sample():
    app = _load_app_module()
    ruta = _samples_dir() / "ticket.txt"
    assert ruta.exists()
    categoria = app.classify_document(str(ruta))
    assert categoria == "ticket"


def test_archivo_inexistente():
    app = _load_app_module()
    ruta = _samples_dir() / "no_existe.txt"
    with pytest.raises(FileNotFoundError):
        app.classify_document(str(ruta))