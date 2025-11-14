const bookList = document.getElementById('book-list');
const searchInput = document.getElementById('search');
const bookAnchors = document.getElementsByClassName('book');

searchInput.addEventListener('input', e => {
    
    let bookListContent = '';

    Array.from(bookAnchors).forEach( book => {

        const title = book.innerText;
        const href = book.href;
        if ( title.toLowerCase().includes(searchInput.value.toLowerCase()) ) {

            bookListContent += `<li><a class="book" href="${href}">${title}</a></li>`;
            
        } else {

            bookListContent += `<li class="hidden"><a class="book" href="${href}">${title}</a></li>`;
        }

    });

    bookList.innerHTML = bookListContent;
});