/**
 * Funciones para exportar datos a PDF y Excel
 */

// Esperar a que se carguen las bibliotecas necesarias
function waitForLibraries() {
    return new Promise((resolve) => {
        if (window.jspdf && window.jspdf.jsPDF && window.XLSX) {
            resolve();
        } else {
            setTimeout(() => waitForLibraries().then(resolve), 100);
        }
    });
}

/**
 * Exporta los datos actuales a PDF
 */
async function exportToPDF() {
    try {
        // Mostrar carga
        showLoading('Generando informe PDF...');
        
        // Esperar a que se carguen las bibliotecas
        await waitForLibraries();
        
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 15;
        
        // Título del informe
        doc.setFontSize(18);
        doc.setFont('helvetica', 'bold');
        doc.text('INFORME DE ELEMENTOS REGISTRADOS', pageWidth / 2, 20, { align: 'center' });
        
        // Información de fecha y hora
        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        doc.text(`Generado el: ${new Date().toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: 'long', 
            year: 'numeric' 
        })} a las ${new Date().toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        })}`, margin, 35);
        
        // Obtener datos de los elementos
        const elementos = Array.from(document.querySelectorAll('.elemento-card'));
        const data = elementos.map(el => ({
            codigo: el.getAttribute('data-codigo') || '',
            tipo: el.getAttribute('data-tipo') || '',
            marca: el.getAttribute('data-marca') || '',
            modelo: el.getAttribute('data-modelo') || '',
            hora: el.querySelector('.text-gray-500')?.textContent.replace('🕒', '').trim() || '',
            estado: el.querySelector('.px-2')?.textContent.trim() || ''
        }));
        
        // Configurar la tabla
        const headers = [['Código', 'Tipo', 'Marca', 'Modelo', 'Hora', 'Estado']];
        const tableData = data.map(item => [
            item.codigo,
            item.tipo,
            item.marca,
            item.modelo,
            item.hora,
            item.estado
        ]);
        
        // Agregar tabla al PDF
        doc.autoTable({
            head: headers,
            body: tableData,
            startY: 50,
            margin: { left: margin, right: margin },
            styles: { 
                fontSize: 9,
                cellPadding: 2,
                lineColor: [0, 0, 0],
                lineWidth: 0.1,
                textColor: [0, 0, 0]
            },
            headStyles: {
                fillColor: [41, 128, 185],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            didDrawPage: function(data) {
                // Pie de página
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFontSize(8);
                doc.setTextColor(150);
                doc.text(
                    `Página ${data.pageNumber} de ${pageCount}`,
                    pageWidth / 2,
                    doc.internal.pageSize.getHeight() - 10,
                    { align: 'center' }
                );
                doc.text(
                    'Generado por Sistema de Portería - LOA TECH',
                    pageWidth - margin,
                    doc.internal.pageSize.getHeight() - 10,
                    { align: 'right' }
                );
            }
        });
        
        // Guardar el PDF
        const fechaActual = new Date().toISOString().split('T')[0];
        doc.save(`informe_elementos_${fechaActual}.pdf`);
        
        hideLoading();
        showMessage('success', 'El informe PDF se ha generado correctamente.');
        
    } catch (error) {
        console.error('Error al generar PDF:', error);
        hideLoading();
        showMessage('error', 'Error al generar el informe PDF. Por favor, intente nuevamente.');
    }
}

/**
 * Exporta los datos actuales a Excel
 */
async function exportToExcel() {
    try {
        showLoading('Generando archivo Excel...');
        
        // Esperar a que se cargue la biblioteca
        await waitForLibraries();
        
        // Obtener datos de los elementos
        const elementos = Array.from(document.querySelectorAll('.elemento-card'));
        const data = elementos.map(el => ({
            'Código': el.getAttribute('data-codigo') || '',
            'Tipo': el.getAttribute('data-tipo') || '',
            'Marca': el.getAttribute('data-marca') || '',
            'Modelo': el.getAttribute('data-modelo') || '',
            'Hora': el.querySelector('.text-gray-500')?.textContent.replace('🕒', '').trim() || '',
            'Estado': el.querySelector('.px-2')?.textContent.trim() || ''
        }));
        
        // Crear un nuevo libro de trabajo
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet(data);
        
        // Ajustar el ancho de las columnas
        const wscols = [
            { wch: 15 }, // Código
            { wch: 15 }, // Tipo
            { wch: 20 }, // Marca
            { wch: 30 }, // Modelo
            { wch: 15 }, // Hora
            { wch: 15 }  // Estado
        ];
        ws['!cols'] = wscols;
        
        // Agregar título y metadatos
        const titulo = 'INFORME DE ELEMENTOS REGISTRADOS';
        const fecha = new Date().toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: 'long', 
            year: 'numeric' 
        });
        
        // Agregar filas de encabezado
        XLSX.utils.sheet_add_aoa(ws, [[titulo]], { origin: 'A1' });
        XLSX.utils.sheet_add_aoa(ws, [[`Generado el: ${fecha} a las ${new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}`]], { origin: 'A2' });
        XLSX.utils.sheet_add_json(ws, data, { origin: 'A4', skipHeader: false });
        
        // Combinar celdas para el título
        if (!ws['!merges']) ws['!merges'] = [];
        ws['!merges'].push({ s: { r: 0, c: 0 }, e: { r: 0, c: 5 } });
        ws['!merges'].push({ s: { r: 1, c: 0 }, e: { r: 1, c: 5 } });
        
        // Agregar la hoja al libro
        XLSX.utils.book_append_sheet(wb, ws, 'Elementos');
        
        // Generar el archivo Excel
        const fechaActual = new Date().toISOString().split('T')[0];
        XLSX.writeFile(wb, `informe_elementos_${fechaActual}.xlsx`);
        
        hideLoading();
        showMessage('success', 'El archivo Excel se ha generado correctamente.');
        
    } catch (error) {
        console.error('Error al generar Excel:', error);
        hideLoading();
        showMessage('error', 'Error al generar el archivo Excel. Por favor, intente nuevamente.');
    }
}

/**
 * Muestra un mensaje de carga
 * @param {string} message - Mensaje a mostrar
 */
function showLoading(message = 'Cargando...') {
    let overlay = document.getElementById('loading-overlay');
    
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        overlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl">
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mr-3"></div>
                    <p class="text-gray-800">${message}</p>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
    } else {
        const messageElement = overlay.querySelector('p');
        if (messageElement) {
            messageElement.textContent = message;
        }
    }
}

/**
 * Oculta el mensaje de carga
 */
function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
        document.body.style.overflow = '';
    }
}

/**
 * Muestra un mensaje de notificación
 * @param {string} type - Tipo de mensaje (success, error, warning, info)
 * @param {string} message - Mensaje a mostrar
 */
function showMessage(type, message) {
    const types = {
        success: {
            bg: 'bg-green-100 border-green-500 text-green-700',
            icon: 'fa-check-circle',
            title: 'Éxito'
        },
        error: {
            bg: 'bg-red-100 border-red-500 text-red-700',
            icon: 'fa-times-circle',
            title: 'Error'
        },
        warning: {
            bg: 'bg-yellow-100 border-yellow-500 text-yellow-700',
            icon: 'fa-exclamation-triangle',
            title: 'Advertencia'
        },
        info: {
            bg: 'bg-blue-100 border-blue-500 text-blue-700',
            icon: 'fa-info-circle',
            title: 'Información'
        }
    };
    
    const config = types[type] || types.info;
    
    const messageElement = document.createElement('div');
    messageElement.className = `fixed top-4 right-4 border-l-4 p-4 ${config.bg} shadow-lg rounded max-w-sm z-50 flex items-start`;
    messageElement.role = 'alert';
    messageElement.innerHTML = `
        <i class="fas ${config.icon} text-lg mr-3 mt-0.5"></i>
        <div>
            <p class="font-bold">${config.title}</p>
            <p class="text-sm">${message}</p>
        </div>
        <button class="ml-4 text-gray-500 hover:text-gray-700" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(messageElement);
    
    // Eliminar el mensaje después de 5 segundos
    setTimeout(() => {
        if (messageElement.parentNode) {
            messageElement.remove();
        }
    }, 5000);
}

// Exportar funciones al ámbito global
window.exportToPDF = exportToPDF;
window.exportToExcel = exportToExcel;
