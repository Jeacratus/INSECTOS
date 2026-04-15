class Tarea {
    constructor(descripcion) {
        this.descripcion=descripcion;
        this.completado=false;
    }

    toggleCompletado() {
        this.completado= !this.completado;
    }
}

class ListaTareas {
    constructor() {
        this.tareas=[];
    }

    agregarTarea(descripcion) {
        const tarea=new Tarea(descripcion);
        this.tareas.push(tarea);
        this.mostrarTareas();
    }

    eliminarTarea(index) {
        this.tareas.splice(index, 1);
        this.mostrarTareas();
    }

    mostrarTareas(){
        const listatareas= document.getElementById("listatareas");
        listatareas.innerHTML='';
        
        this.tareas.forEach((tareas,index) => {
            const li= document.createElement("li");
            li.classList.add("li_2");
            li.textContent = tareas.descripcion;

            if(tareas.completado)
            {
                li.style.textDecoration="line-through";
            }

            const deleteButton=document.createElement("button");
            deleteButton.textContent="Eliminar";
            deleteButton.addEventListener("click", () => this.eliminarTarea(index));

            li.appendChild(deleteButton);
            listatareas.appendChild(li);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = 'http://localhost/proyecto%20del%20cuatrimestre/comentario.php';

    // Function to load comments for a specific insect
    async function loadComments(container) {
        const insecto = container.dataset.insecto;
        const ul = container.querySelector('.listaComentarios');
        try {
            const response = await fetch(`${apiUrl}?insecto=${encodeURIComponent(insecto)}`);
            const comments = await response.json();
            
            ul.innerHTML = ''; // Clear existing
            comments.forEach(comment => {
                const li = document.createElement('li');
                li.textContent = `${comment.nombre}: ${comment.texto}`;
                ul.appendChild(li);
            });
        } catch (error) {
            console.error('Error loading comments for ' + insecto, error);
        }
    }

    document.querySelectorAll('.contenedor_comentario').forEach(container => {
        loadComments(container);
    });

    // Add event listeners for "Mostrar Comentarios" buttons
    const mostrarButtons = document.querySelectorAll('.mostrarComentarios');
    mostrarButtons.forEach(button => {
        button.addEventListener('click', () => {
            const container = button.closest('.contenedor_comentario');
            loadComments(container);
        });
    });
    const forms = document.querySelectorAll('.formComentario');
    
    forms.forEach(form => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            
            const nombreInput = form.querySelector('input[name="nombre"]');
            const textoInput = form.querySelector('input[name="texto"]');
            const insectoInput = form.querySelector('input[name="insecto"]');
            
            const nombre = nombreInput.value.trim();
            const texto = textoInput.value.trim();
            const insecto = insectoInput.value;
            
            if (!nombre || !texto) {
                alert('Por favor, completa todos los campos.');
                return;
            }
            
            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        nombre: nombre,
                        texto: texto,
                        insecto: insecto
                    })
                });
                
                const result = await response.json();
                
                if (result.message === 'Comentario agregado exitosamente') {
                    // Add to list
                    const ul = form.parentElement.querySelector('.listaComentarios');
                    const li = document.createElement('li');
                    li.textContent = `${nombre}: ${texto}`;
                    ul.appendChild(li);
                    
                    // Clear inputs
                    nombreInput.value = '';
                    textoInput.value = '';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al enviar el comentario.');
            }
        });
    });

    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});