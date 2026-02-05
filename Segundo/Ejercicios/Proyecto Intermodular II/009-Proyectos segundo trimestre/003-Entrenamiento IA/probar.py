import torch
from transformers import AutoModelForCausalLM, AutoTokenizer

# Usamos un modelo ligero para el ejemplo "Qwen/Qwen2.5-0.5B-Instruct"
# En un caso real usaríamos el path a nuestro modelo o adaptador
NOMBRE_MODELO = "Qwen/Qwen2.5-0.5B-Instruct"

def cargar_modelo():
    print("Cargando modelo y tokenizador... Por favor espera.")
    try:
        tokenizer = AutoTokenizer.from_pretrained(NOMBRE_MODELO)
        model = AutoModelForCausalLM.from_pretrained(
            NOMBRE_MODELO,
            # Usamos float32 para compatibilidad con CPU si no hay GPU
            torch_dtype=torch.float32, 
            device_map="auto"
        )
        return model, tokenizer
    except Exception as e:
        print(f"Error al cargar el modelo: {e}")
        return None, None

def main():
    model, tokenizer = cargar_modelo()
    
    if not model or not tokenizer:
        return

    print("\n" + "="*50)
    print("MODELO LISTO PARA PRUEBAS")
    print("="*50)
    print("Escribe tus preguntas en español.")
    print("Escribe 'salir' para terminar el programa.")

    while True:
        pregunta = input("\nTú: ")
        
        if pregunta.lower() == 'salir':
            print("Saliendo...")
            break
        
        # Estructura del mensaje para Qwen
        messages = [
            {"role": "system", "content": "Eres un asistente útil que responde siempre en español de forma concisa."},
            {"role": "user", "content": pregunta}
        ]
        
        # Preparar input
        text = tokenizer.apply_chat_template(
            messages,
            tokenize=False,
            add_generation_prompt=True
        )
        
        inputs = tokenizer([text], return_tensors="pt").to(model.device)
        
        # Generar respuesta
        with torch.no_grad():
            generated_ids = model.generate(
                **inputs,
                max_new_tokens=256,
                temperature=0.7,
                do_sample=True
            )
            
        # Decodificar salida (quitando los tokens de input)
        generated_ids = [
            output_ids[len(input_ids):] for input_ids, output_ids in zip(inputs.input_ids, generated_ids)
        ]
        response = tokenizer.batch_decode(generated_ids, skip_special_tokens=True)[0]
        
        print(f"IA: {response}")

if __name__ == "__main__":
    main()
