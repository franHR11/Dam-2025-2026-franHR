from jvorm import JsonMySQLBridge

bridge = JsonMySQLBridge(
    host="localhost", 
    user="prueba",
    password="prueba",
    database="prueba",
    json_path_default="./datos.json"  # JSON de entrada
)

bridge.load_from_json()  # usa ./datos.json por defecto

recovered = bridge.dump_to_json("./dump_recuperado.json")

print("✅ Completado. Resumen del primer nivel:")
for k, v in recovered.items():
    print(f"- {k}: {len(v)} elementos")

