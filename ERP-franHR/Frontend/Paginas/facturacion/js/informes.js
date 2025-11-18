class InformesFacturacion {
  constructor() {
    this.estadisticas = {};
    this.charts = {};
    this.elementos = {};

    document.addEventListener("DOMContentLoaded", () => this.init());
  }

  async init() {
    this.cargarElementos();
    this.configurarEventos();
    this.llenarSelectEjercicios();

    try {
      await this.cargarEstadisticas();
      this.renderizarEstadisticas();
      this.crearGraficos();
    } catch (error) {
      console.error(error);
      this.mostrarAlerta("Error al cargar las estadísticas", "danger");
    }
  }

  cargarElementos() {
    this.elementos = {
      ejercicioSelect: document.getElementById("ejercicio-select"),
      tipoSelect: document.getElementById("tipo-select"),
      periodoSelect: document.getElementById("periodo-select"),
      actualizarBtn: document.getElementById("actualizar-btn"),
      exportarBtn: document.getElementById("exportar-informe-btn"),
      imprimirBtn: document.getElementById("imprimir-informe-btn"),
      loadingIndicator: document.getElementById("loading-indicator"),

      // Tarjetas de resumen
      totalGeneradas: document.getElementById("total-generadas"),
      totalPagadas: document.getElementById("total-pagadas"),
      totalPendientes: document.getElementById("total-pendientes"),
      totalVencidas: document.getElementById("total-vencidas"),
      importeTotal: document.getElementById("importe-total"),
      importePendiente: document.getElementById("importe-pendiente"),
      importePromedio: document.getElementById("importe-promedio"),

      // Tablas
      topClientesTbody: document.getElementById("top-clientes-tbody"),
      topProductosTbody: document.getElementById("top-productos-tbody"),
    };
  }

  configurarEventos() {
    this.elementos.actualizarBtn?.addEventListener("click", () =>
      this.actualizarDatos()
    );
    this.elementos.exportarBtn?.addEventListener("click", () =>
      this.exportarDatos()
    );
    this.elementos.imprimirBtn?.addEventListener("click", () =>
      this.imprimirInforme()
    );

    this.elementos.ejercicioSelect?.addEventListener("change", () =>
      this.actualizarDatos()
    );
    this.elementos.tipoSelect?.addEventListener("change", () =>
      this.actualizarDatos()
    );
    this.elementos.periodoSelect?.addEventListener("change", () =>
      this.actualizarDatos()
    );
  }

  llenarSelectEjercicios() {
    const añoActual = new Date().getFullYear();
    const años = [];

    for (let i = añoActual; i >= añoActual - 5; i--) {
      años.push(i);
    }

    this.elementos.ejercicioSelect.innerHTML = años
      .map(
        (año) =>
          `<option value="${año}" ${
            año === añoActual ? "selected" : ""
          }>${año}</option>`
      )
      .join("");
  }

  async cargarEstadisticas() {
    this.mostrarLoading(true);

    const ejercicio = this.elementos.ejercicioSelect.value;
    const tipo = this.elementos.tipoSelect.value;
    const periodo = this.elementos.periodoSelect.value;

    const params = new URLSearchParams({
      ejercicio,
      tipo,
      periodo,
    });

    const response = await fetch(
      `../../api/facturacion/estadisticas.php?${params.toString()}`
    );
    const data = await response.json();

    if (!data.ok) {
      throw new Error(data.error || "Error al cargar estadísticas");
    }

    this.estadisticas = data.estadisticas;
    this.mostrarLoading(false);
  }

  renderizarEstadisticas() {
    const stats = this.estadisticas;

    // Actualizar tarjetas de resumen
    this.elementos.totalGeneradas.textContent =
      stats.generadas.toLocaleString();
    this.elementos.totalPagadas.textContent = stats.pagadas.toLocaleString();
    this.elementos.totalPendientes.textContent =
      stats.pendientes.toLocaleString();
    this.elementos.totalVencidas.textContent = stats.vencidas.toLocaleString();

    this.elementos.importeTotal.textContent = this.formatearMoneda(
      stats.total_facturado
    );
    this.elementos.importePendiente.textContent = this.formatearMoneda(
      stats.total_pendiente
    );
    this.elementos.importePromedio.textContent = this.formatearMoneda(
      stats.promedio_factura
    );

    // Renderizar top clientes
    this.renderizarTopClientes(stats.top_clientes || []);

    // Renderizar top productos
    this.renderizarTopProductos(stats.top_productos || []);
  }

  renderizarTopClientes(clientes) {
    if (!clientes.length) {
      this.elementos.topClientesTbody.innerHTML = `
        <tr>
          <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
        </tr>
      `;
      return;
    }

    this.elementos.topClientesTbody.innerHTML = clientes
      .map(
        (cliente, index) => `
        <tr>
          <td>${cliente.nombre}</td>
          <td class="text-end">${cliente.cantidad_facturas.toLocaleString()}</td>
          <td class="text-end">${this.formatearMoneda(
            cliente.total_facturado
          )}</td>
        </tr>
      `
      )
      .join("");
  }

  renderizarTopProductos(productos) {
    if (!productos.length) {
      this.elementos.topProductosTbody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center text-muted">No hay datos disponibles</td>
        </tr>
      `;
      return;
    }

    this.elementos.topProductosTbody.innerHTML = productos
      .map(
        (producto, index) => `
        <tr>
          <td>${producto.nombre}</td>
          <td class="text-end">${producto.total_unidades.toLocaleString()}</td>
          <td class="text-end">${producto.veces_facturado.toLocaleString()}</td>
          <td class="text-end">${this.formatearMoneda(
            producto.total_importe
          )}</td>
        </tr>
      `
      )
      .join("");
  }

  crearGraficos() {
    this.crearGraficoEstados();
    this.crearGraficoTipos();
    this.crearGraficoEvolucion();
    this.crearGraficoIngresos();
  }

  crearGraficoEstados() {
    const ctx = document.getElementById("estados-chart");
    if (!ctx) return;

    // Destruir gráfico existente
    if (this.charts.estados) {
      this.charts.estados.destroy();
    }

    const datos = this.estadisticas.facturas_por_estado || [];

    this.charts.estados = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: datos.map((d) => d.estado),
        datasets: [
          {
            data: datos.map((d) => d.cantidad),
            backgroundColor: [
              "#4f46e5", // borrador
              "#f59e0b", // pendiente
              "#10b981", // pagada/cobrada
              "#ef4444", // vencida
              "#6b7280", // cancelada
            ],
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
      },
    });
  }

  crearGraficoTipos() {
    const ctx = document.getElementById("tipos-chart");
    if (!ctx) return;

    // Destruir gráfico existente
    if (this.charts.tipos) {
      this.charts.tipos.destroy();
    }

    const datos = this.estadisticas.facturas_por_tipo || [];

    this.charts.tipos = new Chart(ctx, {
      type: "pie",
      data: {
        labels: datos.map((d) => d.tipo),
        datasets: [
          {
            data: datos.map((d) => d.cantidad),
            backgroundColor: [
              "#3b82f6", // venta
              "#8b5cf6", // compra
              "#6366f1", // rectificativa
              "#ec4899", // proforma
            ],
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
      },
    });
  }

  crearGraficoEvolucion() {
    const ctx = document.getElementById("evolucion-chart");
    if (!ctx) return;

    // Destruir gráfico existente
    if (this.charts.evolucion) {
      this.charts.evolucion.destroy();
    }

    const datos = this.estadisticas.evolucion_mensual || [];

    this.charts.evolucion = new Chart(ctx, {
      type: "line",
      data: {
        labels: datos.map((d) => {
          const [año, mes] = d.mes.split("-");
          const meses = [
            "Ene",
            "Feb",
            "Mar",
            "Abr",
            "May",
            "Jun",
            "Jul",
            "Ago",
            "Sep",
            "Oct",
            "Nov",
            "Dic",
          ];
          return `${meses[parseInt(mes) - 1]} ${año}`;
        }),
        datasets: [
          {
            label: "Facturas",
            data: datos.map((d) => d.cantidad),
            borderColor: "#4f46e5",
            backgroundColor: "rgba(79, 70, 229, 0.1)",
            tension: 0.4,
          },
          {
            label: "Importe",
            data: datos.map((d) => d.importe),
            borderColor: "#10b981",
            backgroundColor: "rgba(16, 185, 129, 0.1)",
            tension: 0.4,
            yAxisID: "y1",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: "index",
          intersect: false,
        },
        scales: {
          y: {
            type: "linear",
            display: true,
            position: "left",
          },
          y1: {
            type: "linear",
            display: true,
            position: "right",
            grid: {
              drawOnChartArea: false,
            },
          },
        },
      },
    });
  }

  crearGraficoIngresos() {
    const ctx = document.getElementById("ingresos-chart");
    if (!ctx) return;

    // Destruir gráfico existente
    if (this.charts.ingresos) {
      this.charts.ingresos.destroy();
    }

    const datos = this.estadisticas.ingresos_mensuales || [];
    const meses = [
      "Ene",
      "Feb",
      "Mar",
      "Abr",
      "May",
      "Jun",
      "Jul",
      "Ago",
      "Sep",
      "Oct",
      "Nov",
      "Dic",
    ];

    this.charts.ingresos = new Chart(ctx, {
      type: "bar",
      data: {
        labels: meses,
        datasets: [
          {
            label: "Ingresos",
            data: Array(12)
              .fill(0)
              .map((_, index) => {
                const mesDatos = datos.find((d) => d.mes === index + 1);
                return mesDatos ? mesDatos.ingresos : 0;
              }),
            backgroundColor: "rgba(16, 185, 129, 0.8)",
            borderColor: "#10b981",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return "€" + value.toLocaleString();
              },
            },
          },
        },
      },
    });
  }

  async actualizarDatos() {
    try {
      await this.cargarEstadisticas();
      this.renderizarEstadisticas();
      this.crearGraficos();
      this.mostrarAlerta("Estadísticas actualizadas correctamente", "success");
    } catch (error) {
      console.error(error);
      this.mostrarAlerta("Error al actualizar las estadísticas", "danger");
    }
  }

  exportarDatos() {
    const datos = this.estadisticas;
    const csv = this.convertirACSV(datos);
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);

    link.setAttribute("href", url);
    link.setAttribute(
      "download",
      `estadisticas_facturacion_${new Date().toISOString().split("T")[0]}.csv`
    );
    link.style.visibility = "hidden";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    this.mostrarAlerta("Datos exportados correctamente", "success");
  }

  imprimirInforme() {
    window.print();
    this.mostrarAlerta("Preparando impresión...", "info");
  }

  convertirACSV(datos) {
    let csv = "SEP=,";
    csv += "\r\n\r\n";

    // Cabecera
    csv += "Estadísticas de Facturación\r\n";
    csv += `Generado: ${new Date().toLocaleString()}\r\n`;
    csv += `Ejercicio: ${datos.ejercicio}\r\n`;
    csv += `Tipo: ${datos.tipo}\r\n`;
    csv += `Período: ${datos.periodo}\r\n\r\n`;

    // Resumen
    csv += "Resumen\r\n";
    csv += "Métrica,Valor\r\n";
    csv += `Facturas Generadas,${datos.generadas}\r\n`;
    csv += `Facturas Pagadas,${datos.pagadas}\r\n`;
    csv += `Facturas Pendientes,${datos.pendientes}\r\n`;
    csv += `Facturas Vencidas,${datos.vencidas}\r\n`;
    csv += `Total Facturado,${datos.total_facturado}\r\n`;
    csv += `Total Pendiente,${datos.total_pendiente}\r\n`;
    csv += `Promedio por Factura,${datos.promedio_factura}\r\n\r\n`;

    // Top Clientes
    if (datos.top_clientes && datos.top_clientes.length > 0) {
      csv += "Top Clientes\r\n";
      csv += "Cliente,Facturas,Importe\r\n";
      datos.top_clientes.forEach((cliente) => {
        csv += `"${cliente.nombre}",${cliente.cantidad_facturas},${cliente.total_facturado}\r\n`;
      });
      csv += "\r\n";
    }

    // Top Productos
    if (datos.top_productos && datos.top_productos.length > 0) {
      csv += "Top Productos\r\n";
      csv += "Producto,Unidades,Veces,Importe\r\n";
      datos.top_productos.forEach((producto) => {
        csv += `"${producto.nombre}",${producto.total_unidades},${producto.veces_facturado},${producto.total_importe}\r\n`;
      });
    }

    return csv;
  }

  mostrarLoading(mostrar) {
    if (this.elementos.loadingIndicator) {
      this.elementos.loadingIndicator.style.display = mostrar
        ? "block"
        : "none";
    }
  }

  formatearMoneda(valor) {
    const number = parseFloat(valor) || 0;
    return number.toLocaleString("es-ES", {
      style: "currency",
      currency: "EUR",
    });
  }

  mostrarAlerta(mensaje, tipo = "info") {
    const alert = document.createElement("div");
    alert.className = `alert alert-${tipo} fade-in`;
    alert.innerHTML = mensaje;
    document.querySelector("#informes-content")?.prepend(alert);
    setTimeout(() => alert.remove(), 4000);
  }
}

const informesFacturacion = new InformesFacturacion();
window.informesFacturacion = informesFacturacion;
