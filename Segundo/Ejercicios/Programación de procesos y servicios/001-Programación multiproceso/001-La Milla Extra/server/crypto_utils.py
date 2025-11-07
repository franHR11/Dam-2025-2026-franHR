# server/crypto_utils.py
import base64
import os
import sys

# Añado el path del servidor al sys.path para importar los módulos
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from Crypto.Cipher import AES, PKCS1_OAEP
from Crypto.PublicKey import RSA
from Crypto.Random import get_random_bytes


def generate_rsa_key_pair():
    # Genero un par de claves RSA de 2048 bits
    key = RSA.generate(2048)
    private_key = key.export_key()
    public_key = key.publickey().export_key()
    return private_key, public_key


def generate_aes_key():
    # Genero una clave AES de 256 bits (32 bytes)
    return get_random_bytes(32)


def encrypt_aes(message, key):
    # Cifro un mensaje con AES en modo GCM (más seguro)
    cipher = AES.new(key, AES.MODE_GCM)
    ciphertext, tag = cipher.encrypt_and_digest(message.encode())
    # Combino nonce + tag + ciphertext para enviarlo todo junto
    return base64.b64encode(cipher.nonce + tag + ciphertext)


def decrypt_aes(encrypted_message, key):
    # Descifro un mensaje con AES en modo GCM
    data = base64.b64decode(encrypted_message)
    nonce = data[:16]  # GCM nonce es de 16 bytes
    tag = data[16:32]  # GCM tag es de 16 bytes
    ciphertext = data[32:]  # El resto es el ciphertext

    cipher = AES.new(key, AES.MODE_GCM, nonce=nonce)
    # Verifica y descifra en una sola operación
    return cipher.decrypt_and_verify(ciphertext, tag).decode()


def encrypt_rsa(data, public_key):
    # Cifro datos con clave pública RSA usando OAEP padding
    rsa_key = RSA.import_key(public_key)
    cipher = PKCS1_OAEP.new(rsa_key)
    return cipher.encrypt(data)


def decrypt_rsa(encrypted_data, private_key):
    # Descifro datos con clave privada RSA usando OAEP padding
    rsa_key = RSA.import_key(private_key)
    cipher = PKCS1_OAEP.new(rsa_key)
    return cipher.decrypt(encrypted_data)


def test_crypto():
    # Función de prueba para verificar que todo funciona
    print("Probando sistema criptográfico...")

    # Prueba RSA
    private_key, public_key = generate_rsa_key_pair()
    test_data = b"Hola mundo RSA"
    encrypted_rsa = encrypt_rsa(test_data, public_key)
    decrypted_rsa = decrypt_rsa(encrypted_rsa, private_key)
    assert decrypted_rsa == test_data, "RSA test failed"
    print("✓ RSA funciona correctamente")

    # Prueba AES
    aes_key = generate_aes_key()
    test_message = "Hola mundo AES"
    encrypted_aes = encrypt_aes(test_message, aes_key)
    decrypted_aes = decrypt_aes(encrypted_aes, aes_key)
    assert decrypted_aes == test_message, "AES test failed"
    print("✓ AES funciona correctamente")

    print("✓ Sistema criptográfico verificado exitosamente")


if __name__ == "__main__":
    test_crypto()
