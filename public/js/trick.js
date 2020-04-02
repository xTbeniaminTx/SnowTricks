$('#add-image').click(function () {
    // count the futures fields that will be created
    const index = +$('#widgets-counter').val();
    console.log(index);
    // we get the entries prototype
    const tmlp = $('#trick_images').data('prototype').replace(/__name__/g, index);
    // injection du template dans le div
    $('#trick_images').append(tmlp);
    //
    $('#widgets-counter').val(index + 1);
    // delete button function call
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#trick_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();