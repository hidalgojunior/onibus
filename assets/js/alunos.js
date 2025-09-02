// Funções para gestão de alunos
console.log('Arquivo alunos.js carregado com sucesso!');

let selectedStudents = new Set();
let currentStudentId = null;

// Aguardar o DOM estar pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado, funções de alunos prontas!');
});

function filterStudents() {
    const searchTerm = document.getElementById("searchInput").value.toLowerCase();
    const statusFilter = document.getElementById("statusFilter").value;
    const schoolFilter = document.getElementById("schoolFilter").value;
    const routeFilter = document.getElementById("routeFilter").value;
    
    const studentCards = document.querySelectorAll(".student-card");
    
    studentCards.forEach(card => {
        const name = card.querySelector(".student-name").textContent.toLowerCase();
        const school = card.querySelector(".student-school").textContent.toLowerCase();
        const curso = card.textContent.toLowerCase();
        const cardText = card.textContent.toLowerCase();
        
        let showCard = true;
        
        if (searchTerm && !name.includes(searchTerm) && !cardText.includes(searchTerm)) {
            showCard = false;
        }
        
        if (statusFilter && !curso.includes(statusFilter.toLowerCase())) {
            showCard = false;
        }
        
        if (schoolFilter && !school.includes(schoolFilter)) {
            showCard = false;
        }
        
        if (routeFilter && !cardText.includes(routeFilter.toLowerCase())) {
            showCard = false;
        }
        
        card.style.display = showCard ? "block" : "none";
    });
}

function clearFilters() {
    document.getElementById("searchInput").value = "";
    document.getElementById("statusFilter").value = "";
    document.getElementById("schoolFilter").value = "";
    document.getElementById("routeFilter").value = "";
    filterStudents();
}

function addStudent() {
    const modal = new bootstrap.Modal(document.getElementById("addStudentModal"));
    modal.show();
}

function deleteStudent(id) {
    console.log('deleteStudent chamado com ID:', id);
    
    document.getElementById("deleteStudentId").value = id;
    const modal = new bootstrap.Modal(document.getElementById("deleteStudentModal"));
    modal.show();
}

function toggleStudentSelection(id, checkbox) {
    if (checkbox.checked) {
        selectedStudents.add(id);
    } else {
        selectedStudents.delete(id);
    }
    
    updateBulkActions();
}

function updateBulkActions() {
    const bulkActions = document.getElementById("bulkActions");
    const bulkInfo = document.getElementById("bulkInfo");
    
    if (selectedStudents.size > 0) {
        bulkActions.style.display = "flex";
        bulkInfo.textContent = selectedStudents.size + " aluno(s) selecionado(s)";
    } else {
        bulkActions.style.display = "none";
    }
}

function selectAllStudents() {
    const checkboxes = document.querySelectorAll('.student-card input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        const id = parseInt(checkbox.value);
        selectedStudents.add(id);
    });
    
    updateBulkActions();
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.student-card input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    selectedStudents.clear();
    updateBulkActions();
}

function assignRoute() {
    if (selectedStudents.size === 0) return;
    alert("Atribuir rota para " + selectedStudents.size + " aluno(s)");
}

function changeStatus() {
    if (selectedStudents.size === 0) return;
    alert("Alterar status de " + selectedStudents.size + " aluno(s)");
}

function exportStudents() {
    alert("Exportar dados dos alunos para Excel");
}

function importStudents() {
    alert("Importar alunos via planilha");
}

// Funções específicas para modais - serão sobrescritas pelos dados reais
function editStudent(id) {
    console.log('editStudent chamado com ID:', id);
    
    if (typeof alunosData !== 'undefined') {
        const aluno = alunosData.find(a => a.id == id);
        if (!aluno) return;
        
        document.getElementById("editStudentContent").innerHTML = `
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="${aluno.id}">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome Completo *</label>
                    <input type="text" class="form-control" name="nome" value="${aluno.nome || ""}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">RG</label>
                    <input type="text" class="form-control" name="rg" value="${aluno.rg || ""}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">RM</label>
                    <input type="text" class="form-control" name="rm" value="${aluno.rm || ""}">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Série *</label>
                    <input type="text" class="form-control" name="serie" value="${aluno.serie || ""}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Curso *</label>
                    <input type="text" class="form-control" name="curso" value="${aluno.curso || ""}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" name="data_aniversario" value="${aluno.data_aniversario || ""}">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" value="${aluno.telefone || ""}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Telefone de Emergência</label>
                    <input type="text" class="form-control" name="telefone_emergencia" value="${aluno.telefone_emergencia || ""}">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nome do Responsável</label>
                    <input type="text" class="form-control" name="responsavel_nome" value="${aluno.responsavel_nome || ""}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Telefone do Responsável</label>
                    <input type="text" class="form-control" name="responsavel_telefone" value="${aluno.responsavel_telefone || ""}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">WhatsApp do Responsável</label>
                    <input type="text" class="form-control" name="responsavel_whatsapp" value="${aluno.responsavel_whatsapp || ""}">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Endereço Completo</label>
                    <textarea class="form-control" name="endereco_completo" rows="2">${aluno.endereco_completo || ""}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ponto de Embarque</label>
                    <input type="text" class="form-control" name="ponto_embarque" value="${aluno.ponto_embarque || ""}">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Observações Médicas</label>
                <textarea class="form-control" name="observacoes_medicas" rows="2">${aluno.observacoes_medicas || ""}</textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="whatsapp_permissao" id="edit_whatsapp_permissao" ${aluno.whatsapp_permissao == "1" ? "checked" : ""}>
                        <label class="form-check-label" for="edit_whatsapp_permissao">
                            Permite contato via WhatsApp
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="autorizacao_transporte" id="edit_autorizacao_transporte" ${aluno.autorizacao_transporte == "1" ? "checked" : ""}>
                        <label class="form-check-label" for="edit_autorizacao_transporte">
                            Autorização para transporte
                        </label>
                    </div>
                </div>
            </div>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById("editStudentModal"));
        modal.show();
    } else {
        alert("Função de edição será implementada quando os dados estiverem carregados");
    }
}

function viewStudent(id) {
    console.log('viewStudent chamado com ID:', id);
    
    if (typeof alunosData !== 'undefined') {
        const aluno = alunosData.find(a => a.id == id);
        if (!aluno) return;
        
        const formatDate = (dateStr) => {
            if (!dateStr) return "Não informado";
            const date = new Date(dateStr);
            return date.toLocaleDateString("pt-BR");
        };
        
        document.getElementById("viewStudentContent").innerHTML = `
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="d-flex align-items-center">
                        <div class="student-avatar-large me-3" style="width: 80px; height: 80px; font-size: 2rem; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            ${aluno.nome.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h4 class="mb-1">${aluno.nome}</h4>
                            <p class="text-muted mb-0">${aluno.serie} - ${aluno.curso}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Dados Pessoais</h6>
                    <p><strong>RG:</strong> ${aluno.rg || "Não informado"}</p>
                    <p><strong>RM:</strong> ${aluno.rm || "Não informado"}</p>
                    <p><strong>Data de Nascimento:</strong> ${formatDate(aluno.data_aniversario)}</p>
                    <p><strong>Telefone:</strong> ${aluno.telefone || "Não informado"}</p>
                    <p><strong>Telefone de Emergência:</strong> ${aluno.telefone_emergencia || "Não informado"}</p>
                </div>
                <div class="col-md-6">
                    <h6>Responsável</h6>
                    <p><strong>Nome:</strong> ${aluno.responsavel_nome || "Não informado"}</p>
                    <p><strong>Telefone:</strong> ${aluno.responsavel_telefone || "Não informado"}</p>
                    <p><strong>WhatsApp:</strong> ${aluno.responsavel_whatsapp || "Não informado"}</p>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Endereço e Transporte</h6>
                    <p><strong>Endereço:</strong> ${aluno.endereco_completo || "Não informado"}</p>
                    <p><strong>Ponto de Embarque:</strong> ${aluno.ponto_embarque || "Não informado"}</p>
                </div>
                <div class="col-md-6">
                    <h6>Observações</h6>
                    <p><strong>Obs. Médicas:</strong> ${aluno.observacoes_medicas || "Nenhuma"}</p>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Permissões</h6>
                    <div class="d-flex gap-4">
                        <span class="badge ${aluno.whatsapp_permissao == "1" ? "bg-success" : "bg-secondary"}">
                            ${aluno.whatsapp_permissao == "1" ? "✓" : "✗"} WhatsApp
                        </span>
                        <span class="badge ${aluno.autorizacao_transporte == "1" ? "bg-success" : "bg-secondary"}">
                            ${aluno.autorizacao_transporte == "1" ? "✓" : "✗"} Transporte
                        </span>
                    </div>
                </div>
            </div>
        `;
        
        currentStudentId = id;
        const modal = new bootstrap.Modal(document.getElementById("viewStudentModal"));
        modal.show();
    } else {
        alert("Função de visualização será implementada quando os dados estiverem carregados");
    }
}

function editCurrentStudent() {
    if (currentStudentId) {
        const viewModal = bootstrap.Modal.getInstance(document.getElementById("viewStudentModal"));
        viewModal.hide();
        
        setTimeout(() => {
            editStudent(currentStudentId);
        }, 300);
    }
}
