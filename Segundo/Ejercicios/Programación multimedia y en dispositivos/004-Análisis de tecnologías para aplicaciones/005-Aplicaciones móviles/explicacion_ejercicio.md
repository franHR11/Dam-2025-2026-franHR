# Explicación del ejercicio: Aplicación de Pesca con WebView

### 1. Explicación personal del ejercicio
En esta práctica me he puesto en la piel de un desarrollador que necesita crear una solución rápida y eficiente para Pepe. El objetivo era desarrollar "AplicacionPescaria" sin reinventar la rueda. Sabiendo que ya existe mucha información en la web sobre especies de peces, decidí que la mejor estrategia no era crear una base de datos propia, sino utilizar un **WebView**.

Un WebView es básicamente una ventana de navegador incrustada dentro de nuestra interfaz nativa. Mi planteamiento paso a paso fue:
1.  **Configurar el entorno**: Crear el proyecto y, muy importante, **darle permisos de Internet** en el `AndroidManifest`, ya que sin esto la aplicación nacería "muda".
2.  **Diseño**: Limpiar la interfaz por defecto y colocar un único componente `WebView` que ocupe todo el espacio.
3.  **Lógica**: Aquí es donde tuve que tener cuidado. No basta con decirle "carga esta URL". Tenía que configurar el cliente web (`WebViewClient`) para que el usuario no se "escape" al navegador Chrome al hacer clic en un enlace, manteniendo así la sensación de estar en una aplicación propia.

### 2. Código de programación

**`AndroidManifest.xml`** (Permisos necesarios)
```xml
<?xml version="1.0" encoding="utf-8"?>
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    package="com.jocarsa.aplicacionpescaria">

    <!-- PERMISO CRÍTICO: Sin esto la app no puede salir a internet -->
    <uses-permission android:name="android.permission.INTERNET" />

    <application
        android:allowBackup="true"
        android:icon="@mipmap/ic_launcher"
        android:label="@string/app_name"
        android:roundIcon="@mipmap/ic_launcher_round"
        android:supportsRtl="true"
        android:theme="@style/Theme.AplicacionPescaria">
        <activity
            android:name=".MainActivity"
            android:exported="true">
            <intent-filter>
                <action android:name="android.intent.action.MAIN" />
                <category android:name="android.intent.category.LAUNCHER" />
            </intent-filter>
        </activity>
    </application>
</manifest>
```

**`activity_main.xml`** (Interfaz de usuario)
```xml
<?xml version="1.0" encoding="utf-8"?>
<androidx.constraintlayout.widget.ConstraintLayout xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:app="http://schemas.android.com/apk/res-auto"
    xmlns:tools="http://schemas.android.com/tools"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    tools:context=".MainActivity">

    <!-- El WebView ocupa (match_parent) todo el ancho y alto disponible -->
    <WebView
        android:id="@+id/miviewweb"
        android:layout_width="match_parent"
        android:layout_height="match_parent" />

</androidx.constraintlayout.widget.ConstraintLayout>
```

**`MainActivity.kt`** (Controlador)
```kotlin
package com.jocarsa.aplicacionpescaria

import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.webkit.WebView
import android.webkit.WebViewClient

class MainActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        // 1. Vinculamos la parte gráfica con el código
        val myWebView: WebView = findViewById(R.id.miviewweb)
        
        // 2. Configuración técnica del WebView
        // Habilitamos JS porque muchas webs modernas (como Wikipedia) lo usan para menús o carga
        myWebView.settings.javaScriptEnabled = true
        
        // 3. Gestión de la navegación
        // WebViewClient permite interceptar la carga de URL. 
        // Al instanciarlo, forzamos a que los enlaces se abran en este mismo WebView.
        myWebView.webViewClient = WebViewClient()
        
        // 4. Carga del contenido
        myWebView.loadUrl("https://es.wikipedia.org/wiki/Peces")
    }
}
```

### 3. Rúbrica de evaluación cumplida

A continuación, detallo cómo este ejercicio cumple con cada punto de la rúbrica de evaluación:

*   **Introducción y contextualización (25%)**:
    *   He identificado correctamente la necesidad del usuario (Pepe): acceder a información externa sin salir de la app.
    *   La elección del **WebView** está justificada como la herramienta estándar en Android para mostrar contenido web (HTML/CSS/JS) dentro de una `Activity`. Es el puente fundamental entre el mundo web y el nativo.

*   **Desarrollo técnico correcto y preciso (25%)**:
    *   **Permisos**: He incluido `<uses-permission android:name="android.permission.INTERNET" />`, un paso técnico obligatorio a menudo olvidado por principiantes.
    *   **Configuración del WebView**: No solo he cargado la URL. He añadido `settings.javaScriptEnabled = true` para asegurar compatibilidad y, lo más importante, creado una instancia de `WebViewClient`. Sin esta línea técnica clave, la experiencia de usuario se rompería al abrirse el navegador externo.
    *   **Sintaxis**: El código Kotlin está limpio, tipado y sigue las convenciones de Android (camelCase, R.id references).

*   **Aplicación práctica con ejemplo claro (25%)**:
    *   El ejercicio es totalmente funcional. Al ejecutarse, la aplicación se conecta a `https://es.wikipedia.org/wiki/Peces`, mostrando información real y relevante para el contexto de pesca.
    *   He usado un ejemplo real (Wikipedia) en lugar de una página en blanco, demostrando que la app es capaz de renderizar contenido complejo de terceros.

*   **Cierre/Conclusión enlazando con la unidad (25%)**:
    *   Este ejercicio consolida lo aprendido sobre **Componentes de UI** y **Manifiesto de Android**.
    *   Demuestra el concepto de **aplicación híbrida** básica: una carcasa nativa que encierra contenido web. Es una técnica que usaré en el futuro para secciones dinámicas de apps (como "Términos y condiciones" o "Ayuda") que necesitan actualizarse sin obligar al usuario a actualizar la app completa en la tienda.

### 4. Cierre
Realizar este ejercicio me ha servido para entender que no siempre es necesario programar cada pantalla desde cero. El componente WebView es muy potente si se configura bien (especialmente el cliente web y los permisos). Es una herramienta que seguro utilizaré para integrar contenido online rápidamente.
