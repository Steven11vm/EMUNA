// script.js
document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.querySelector('.formulario');
    
    // Inicializar datepickers
    $(function() {
        $("#inicio, #final").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    });

    // Función para cargar pagos
    async function cargarPagos(event) {
        if (event) event.preventDefault();
        
        const formData = new FormData(formulario);
        const params = new URLSearchParams();
        
        formData.forEach((value, key) => {
            if (value) params.append(key, value);
        });

        try {
            const response = await fetch(`api.php?${params}`);
            const pagos = await response.json();
            mostrarTabla(pagos);
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar los pagos');
        }
    }

    // Función para mostrar la tabla
    function mostrarTabla(pagos) {
        const tabla = `
            <form id="formAprobacion">
                <div class="scrollable-table">
                    <div style="text-align: center; margin: 20px;">
                        <button type="submit" class="btn-aprobar">APROBAR SELECCIONADOS</button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>NIT</th>
                                <th>Fecha</th>
                                <th>Nombre Proveedor</th>
                                <th>Factura</th>
                                <th>Valor Pago</th>
                                <th>PDF</th>
                                <th>Prioridad</th>
                                <th>Nombre Aprobación</th>
                                <th>Aprobar</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pagos.map(pago => `
                                <tr>
                                    <td>${pago.Nit}</td>
                                    <td>${pago.fecha}</td>
                                    <td>${pago.Nombre_proveedor}</td>
                                    <td>${pago.Factura}</td>
                                    <td>${pago.Valor_pago}</td>
                                    <td><a href="pdfs/${pago.Factura}.pdf" target="_blank">Ver PDF</a></td>
                                    <td>${pago.Prioridad}</td>
                                    <td>${pago.Nombre_aprobacion}</td>
                                    <td><input type="checkbox" name="aprobar" value="${pago.Factura}"></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </form>
        `;

        const contenedorTabla = document.querySelector('.scrollable-table');
        if (contenedorTabla) {
            contenedorTabla.innerHTML = tabla;
        } else {
            formulario.insertAdjacentHTML('afterend', tabla);
        }

        // Agregar evento al formulario de aprobación
        document.getElementById('formAprobacion').addEventListener('submit', aprobarPagos);
    }

    // Función para aprobar pagos
    async function aprobarPagos(event) {
        event.preventDefault();
        
        const checkboxes = document.querySelectorAll('input[name="aprobar"]:checked');
        const facturas = Array.from(checkboxes).map(cb => cb.value);

        if (facturas.length === 0) {
            alert('Por favor seleccione al menos un pago para aprobar');
            return;
        }

        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ facturas })
            });

            const resultado = await response.json();
            
            if (resultado.success) {
                alert('Pagos aprobados exitosamente');
                cargarPagos();
            } else {
                alert('Error al aprobar pagos: ' + resultado.message);
                if (resultado.errors && resultado.errors.length > 0) {
                    console.error('Errores:', resultado.errors);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al aprobar los pagos');
        }
    }

    // Event Listeners
    formulario.addEventListener('submit', cargarPagos);
});