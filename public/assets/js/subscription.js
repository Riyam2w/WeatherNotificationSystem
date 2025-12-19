// var indePagePath = ;


document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = new FormData(this);
    console.log('kjdhgjkdfh');
    fetch('index.php?route=subscription/store', {
        method: 'POST',
        body: form
    })
    .then(res => res.json())
    .then(data => {
        const box = document.getElementById('responseBox');
        box.innerHTML = data.message;
        box.style.color = data.success ? 'green' : 'red';
    })
    .catch(err => {console.error(err)});

});