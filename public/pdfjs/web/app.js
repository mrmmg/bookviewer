//test git hooks4
const base_url = window.location.protocol + "//" + window.location.hostname;
const backend_url = base_url + "/api/v1";
const auth_page = base_url + "/admin/login";
const query_params = Object.fromEntries(new URLSearchParams(window.location.search));

document.getElementById('spinner-container').style.display = 'flex';

let response = await isUserLoggedIn();
if(response.message === false){
    alert("You are not logged in, redirect to login page...");
    window.location.href = auth_page;
}

const sleep = (milliseconds) => {
    return new Promise(resolve => setTimeout(resolve, milliseconds))
}
// PDFViewerApplication.store.get('scrollTop')
//PDFViewerApplication.pdfDocument._pdfInfo.fingerprints[0]
//PDFViewerApplication.pdfViewer.currentPageNumber
//PDFViewerApplication.page
// PDFViewerApplication.pdfHistory._position.hash The page and the view position

await sleep(3000);

PDFViewerApplication.eventBus.on('pagenumberchanged', function (event) {
    sendLastPageDataToBackend(event.value);
});

PDFViewerApplication.eventBus.on('pagechanging', function (event) {
    sendLastPageDataToBackend(event.pageNumber);
});

if(query_params.hasOwnProperty('last_page')){
    PDFViewerApplication.pdfViewer._setCurrentPageNumber(query_params.last_page, true);
    await sleep(500);
    showPDF();
} else {
    showPDF();
}

async function sendLastPageDataToBackend(page_number){
    let request_body = {
        hash:  query_params.hash,
        page_number
    }
    let url = backend_url + "/reading/update";

    let response = await fetch(url, {
        method: "POST",
        mode: "cors",
        credentials: "include",
        headers: {
            "Content-Type": "application/json"
        },
        redirect: "follow",
        body: JSON.stringify(request_body)
    });

    response = await response.json();

    if(response.message === false){
        console.error(response.bag);
        alert("Error In update reading data");
    } else {
        console.info("Reading Data Updated...");
    }
}

async function isUserLoggedIn(){
    let url = backend_url + "/user/me"
    let response = await fetch(url)

    return response.json();
}

function showPDF(){
    document.getElementById('spinner-container').style.display = 'none';
}
