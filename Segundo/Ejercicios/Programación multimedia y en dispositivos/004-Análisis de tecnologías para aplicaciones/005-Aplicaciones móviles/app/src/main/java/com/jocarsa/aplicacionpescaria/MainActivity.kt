package com.jocarsa.aplicacionpescaria

import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.webkit.WebView
import android.webkit.WebViewClient

class MainActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val myWebView: WebView = findViewById(R.id.miviewweb)
        // Habilitamos JavaScript para una mejor compatibilidad con webs modernas
        myWebView.settings.javaScriptEnabled = true
        
        // Configuramos el cliente para que los enlaces se abran dentro de la app
        myWebView.webViewClient = WebViewClient()
        
        // Cargamos una web sobre peces (Wikipedia como ejemplo educativo)
        myWebView.loadUrl("https://es.wikipedia.org/wiki/Peces")
    }
}
