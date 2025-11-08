from flask import Flask, render_template, request, jsonify
import mysql.connector
import openai
import threading

app = Flask(__name__)

# Configurar OpenAI (necesitas tu API key)
openai.api_key = 'tu_api_key_aqui'

# Conexión a MySQL
def get_db_connection():
    return mysql.connector.connect(
        host='localhost',
        user='tu_usuario',
        password='tu_password',
        database='erp_db'
    )

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/productos')
def productos():
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute('SELECT * FROM productos')
    productos = cursor.fetchall()
    cursor.close()
    conn.close()
    return render_template('productos.html', productos=productos)

@app.route('/ventas')
def ventas():
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute('SELECT * FROM ventas')
    ventas = cursor.fetchall()
    cursor.close()
    conn.close()
    return render_template('ventas.html', ventas=ventas)

@app.route('/empleados')
def empleados():
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute('SELECT * FROM empleados')
    empleados = cursor.fetchall()
    cursor.close()
    conn.close()
    return render_template('empleados.html', empleados=empleados)

@app.route('/generar_informe', methods=['POST'])
def generar_informe():
    # Usar hilo para procesar en segundo plano
    def procesar_informe():
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute('SELECT SUM(total) FROM ventas')
        total = cursor.fetchone()[0]
        cursor.close()
        conn.close()
        
        prompt = f"Analiza las ventas totales de {total} euros y da consejos para mejorar el negocio."
        response = openai.Completion.create(
            engine="text-davinci-003",
            prompt=prompt,
            max_tokens=150
        )
        informe = response.choices[0].text.strip()
        # Guardar informe en DB o algo, simplificado
        print("Informe generado:", informe)
    
    thread = threading.Thread(target=procesar_informe)
    thread.start()
    return jsonify({"mensaje": "Informe en proceso"})

if __name__ == '__main__':
    app.run(debug=True)
