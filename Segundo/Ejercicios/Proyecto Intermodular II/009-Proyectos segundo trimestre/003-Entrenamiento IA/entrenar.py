from transformers import AutoModelForCausalLM, AutoTokenizer
from peft import LoraConfig, get_peft_model, TaskType
import torch

# Configuración básica para simular el script de entrenamiento
NOMBRE_MODELO = "Qwen/Qwen2.5-0.5B-Instruct"

def preparar_entrenamiento():
    print("Configurando entrenamiento para Qwen2.5...")
    
    # Configuración LoRA
    peft_config = LoraConfig(
        task_type=TaskType.CAUSAL_LM, 
        inference_mode=False, 
        r=8, 
        lora_alpha=32, 
        lora_dropout=0.1
    )
    
    # Cargar modelo base
    print(f"Descargando/Cargando modelo base: {NOMBRE_MODELO}")
    model = AutoModelForCausalLM.from_pretrained(NOMBRE_MODELO)
    
    # Aplicar PEFT
    model = get_peft_model(model, peft_config)
    model.print_trainable_parameters()
    
    print("Modelo listo para iniciar bucle de entrenamiento (Simulado).")

if __name__ == "__main__":
    preparar_entrenamiento()
