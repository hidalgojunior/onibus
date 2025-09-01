/**
 * Sistema de Ônibus - JavaScript Principal
 * Funções comuns e utilitários para todo o sistema
 */

// Configurações globais
const Config = {
    baseUrl: window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/'),
    apiUrl: '/api/',
    timeout: 30000,
    debug: false
};

// Utilitários comuns
const Utils = {
    /**
     * Fazer requisição AJAX com fetch
     */
    async request(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            timeout: Config.timeout
        };

        const finalOptions = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, finalOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            } else {
                return await response.text();
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            throw error;
        }
    },

    /**
     * Exibir notificação toast
     */
    showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        const typeClasses = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-white',
            info: 'bg-blue-500 text-white'
        };

        toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${typeClasses[type]} transition-all duration-300 transform translate-x-full`;
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        // Animar entrada
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Auto remover
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    /**
     * Confirmar ação
     */
    async confirm(message, title = 'Confirmar') {
        return new Promise((resolve) => {
            const modal = this.createModal(title, message, [
                { text: 'Cancelar', class: 'btn-secondary', action: () => resolve(false) },
                { text: 'Confirmar', class: 'btn-danger', action: () => resolve(true) }
            ]);
            document.body.appendChild(modal);
            modal.style.display = 'flex';
        });
    },

    /**
     * Criar modal
     */
    createModal(title, content, buttons = []) {
        const modal = document.createElement('div');
        modal.className = 'modal-backdrop';
        modal.innerHTML = `
            <div class="modal">
                <div class="modal-dialog">
                    <div class="modal-header">
                        <h5 class="text-lg font-semibold">${title}</h5>
                    </div>
                    <div class="modal-body">
                        <p class="text-gray-600">${content}</p>
                    </div>
                    <div class="modal-footer">
                        ${buttons.map(btn => 
                            `<button class="btn-custom ${btn.class}" onclick="this.closest('.modal-backdrop').remove(); (${btn.action})()">${btn.text}</button>`
                        ).join('')}
                    </div>
                </div>
            </div>
        `;

        // Fechar ao clicar fora
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });

        return modal;
    },

    /**
     * Formatar data para exibição
     */
    formatDate(dateString, format = 'DD/MM/YYYY') {
        const date = new Date(dateString);
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');

        switch (format) {
            case 'DD/MM/YYYY':
                return `${day}/${month}/${year}`;
            case 'DD/MM/YYYY HH:mm':
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            default:
                return dateString;
        }
    },

    /**
     * Debounce para otimizar buscas
     */
    debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    },

    /**
     * Loader/Spinner
     */
    showLoader(element) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        element.innerHTML = '<div class="flex justify-center items-center py-4"><div class="spinner"></div></div>';
    },

    hideLoader(element, content = '') {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        element.innerHTML = content;
    }
};

// Componentes reutilizáveis
const Components = {
    /**
     * Criar tabela responsiva
     */
    createTable(data, columns, options = {}) {
        const { actions = [], searchable = false, pagination = false } = options;
        
        let html = '<div class="overflow-x-auto">';
        
        if (searchable) {
            html += `
                <div class="mb-4">
                    <input type="text" 
                           class="form-control max-w-md" 
                           placeholder="Buscar..." 
                           onkeyup="Components.searchTable(this, '${options.tableId || 'datatable'}')">
                </div>
            `;
        }

        html += `
            <table class="table" id="${options.tableId || 'datatable'}">
                <thead>
                    <tr>
                        ${columns.map(col => `<th>${col.title}</th>`).join('')}
                        ${actions.length ? '<th>Ações</th>' : ''}
                    </tr>
                </thead>
                <tbody>
                    ${data.map(row => `
                        <tr>
                            ${columns.map(col => `<td>${row[col.field] || ''}</td>`).join('')}
                            ${actions.length ? `
                                <td>
                                    <div class="flex gap-1">
                                        ${actions.map(action => `
                                            <button class="btn-custom ${action.class}" 
                                                    onclick="${action.onclick}(${JSON.stringify(row).replace(/"/g, '&quot;')})">
                                                <i class="${action.icon}"></i> ${action.text}
                                            </button>
                                        `).join('')}
                                    </div>
                                </td>
                            ` : ''}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        
        html += '</div>';
        return html;
    },

    /**
     * Busca em tabela
     */
    searchTable(input, tableId) {
        const filter = input.value.toLowerCase();
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }

            rows[i].style.display = found ? '' : 'none';
        }
    },

    /**
     * Criar formulário dinâmico
     */
    createForm(fields, options = {}) {
        const { submitText = 'Salvar', onSubmit = null } = options;
        
        let html = `<form class="space-y-4" ${onSubmit ? `onsubmit="${onSubmit}(event)"` : ''}>`;
        
        fields.forEach(field => {
            html += `
                <div>
                    <label class="form-label">${field.label}${field.required ? ' *' : ''}</label>
                    ${this.createFormField(field)}
                </div>
            `;
        });

        html += `
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" class="btn-custom btn-secondary" onclick="this.closest('.modal-backdrop, .card').remove()">
                    Cancelar
                </button>
                <button type="submit" class="btn-custom btn-primary">
                    ${submitText}
                </button>
            </div>
        </form>`;

        return html;
    },

    createFormField(field) {
        switch (field.type) {
            case 'select':
                return `
                    <select name="${field.name}" class="form-select" ${field.required ? 'required' : ''}>
                        <option value="">Selecione...</option>
                        ${field.options.map(opt => 
                            `<option value="${opt.value}" ${opt.selected ? 'selected' : ''}>${opt.text}</option>`
                        ).join('')}
                    </select>
                `;
            case 'textarea':
                return `
                    <textarea name="${field.name}" 
                              class="form-control" 
                              rows="${field.rows || 3}"
                              placeholder="${field.placeholder || ''}"
                              ${field.required ? 'required' : ''}>${field.value || ''}</textarea>
                `;
            default:
                return `
                    <input type="${field.type || 'text'}" 
                           name="${field.name}" 
                           class="form-control"
                           placeholder="${field.placeholder || ''}"
                           value="${field.value || ''}"
                           ${field.required ? 'required' : ''}>
                `;
        }
    }
};

// Inicialização quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Configurar dropdowns
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const toggle = dropdown.querySelector('[data-toggle="dropdown"]');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (toggle && menu) {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                menu.classList.toggle('hidden');
            });

            // Fechar ao clicar fora
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        }
    });

    // Configurar modais
    document.querySelectorAll('[data-modal]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = trigger.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        });
    });

    // Fechar modais
    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    console.log('Sistema de Ônibus - JavaScript carregado com sucesso!');
});

// Exportar para uso global
window.Utils = Utils;
window.Components = Components;
window.Config = Config;
