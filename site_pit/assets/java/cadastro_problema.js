document.addEventListener('DOMContentLoaded', function() {
    // Verifique se a variável 'mensagem' está definida
    if (typeof mensagem !== 'undefined' && mensagem) {
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: mensagem,
            confirmButtonText: 'Fechar'
        });
    }
});
$(document).ready(function() {
    // Inicialize os campos de seleção com a Select2
    $('.select2').select2();
});


