load_section('../../controller/manage-list-categories.php', 'container-secondary');

document.querySelectorAll('.option-submenu').forEach(button => {
    button.addEventListener('click', function () {
        document.querySelectorAll('.option-submenu').forEach(btn => btn.disabled = false);
        this.disabled = true;

        let section = this.dataset.section;
        let path = '../../controller/' + section + '.php';

        load_section(path, 'container-secondary');
    });
});