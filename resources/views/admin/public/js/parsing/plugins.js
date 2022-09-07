
var doc = document;
const url = 'https://app.indema.co';
var pluginObject = {
    active: false,
    images: [],
    priceState: false,
    descriptionState: false,
    pageData: [],
    mouseLeave: false,
    pageTitle: '',
    url: url + '/api-parse/',
    api_key: null,
    activeState: false,

};

async function sendRequest(type, url, data) {
    return await new Promise((resolve, reject) => {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                resolve(true);
            }
        };
        xhr.open(type, url, true);
        xhr.setRequestHeader('Content-type', 'application/json');
        xhr.send(JSON.stringify(data));
    })
}

function firstPageData() {
    pluginObject.images = getPageImages();
    connectIframe().then((res) => {
        if (res) {
            setTimeout(() => {
                setDefaultData().then(resp => {
                    contentClick();
                })
            }, 3500)

        }
    }).catch(err => {
        console.log('err promise', err);
    });
}

if (document.readyState === 'complete') {
    pluginObject.api_key = doc.getElementById('idemia_uuid').value;
    if (pluginObject.api_key) {
        const scriptList = doc.querySelectorAll("script[src='https://app.indema.co/js/parsing/plugins.js']");
        if(scriptList.length > 0) {
            scriptList[0].remove();
        }
        firstPageData()
    }
}

async function connectIframe() {
    return await new Promise(function (resolve, reject) {
        let rightEl = doc.createElement('div');
        rightEl.id = 'rightPanel';
        rightEl.style.position = 'fixed';
        rightEl.style.zIndex = '9999999999';
        rightEl.style.top = '0';
        rightEl.innerHTML += '<div class="radioGroupContainer" id="lastRadioChoicesOne" style="height: 100vh; width: 339px">' +
            '<iframe src="' + pluginObject.url + 'form/' + pluginObject.api_key + '" id="rightPanelIframe" style="height: 100%; width: 100%" frameborder="0">' +
            '<style src="' + url + '/css/parsing/main.js"></style></iframe>' +
            '</div>';
        document.body.append(rightEl);
        resolve(true);
    })
}

async function setDefaultData() {
    return new Promise(function (resolve, reject) {
        if (pluginObject.images && pluginObject.images.length > 0) {
            let imageSrc = pluginObject.images[0].src || pluginObject.images[0].dataset.src;
            
            var imageSrc1 = '';
            if(pluginObject.images[1]) {
                imageSrc1 = pluginObject.images[1].src || pluginObject.images[1].dataset.src;
            }
           
            var imageSrc2 = '';
            if(pluginObject.images[2]) {
                imageSrc2 = pluginObject.images[2].src || pluginObject.images[2].dataset.src;
            }
            
            if(imageSrc.includes("blank")) {
                //console.log(pluginObject.images[2]);
                imageSrc = pluginObject.images[2].src || pluginObject.images[2].dataset.src;
            }
            
            if (imageSrc) {
                pluginObject.pageTitle = getPageTitle();
                pluginObject.activeState = false;
                try {
                    sendRequest('POST', pluginObject.url + 'set-data/' + pluginObject.api_key, {
                        type: 'img',
                        value: imageSrc,
                        text: 'previewBlock'
                    });
                    sendRequest('POST', pluginObject.url + 'set-data/' + pluginObject.api_key, {
                        type: 'img1',
                        value: imageSrc1,
                        text: 'previewBlock'
                    });
                    sendRequest('POST', pluginObject.url + 'set-data/' + pluginObject.api_key, {
                        type: 'img2',
                        value: imageSrc2,
                        text: 'previewBlock'
                    });
                    sendRequest('POST', pluginObject.url + 'set-data/' + pluginObject.api_key, {
                        type: 'pageData',
                        value: window.location.href,
                        text: pluginObject.pageTitle
                    });
                    resolve(true);
                } catch {
                    reject(false)
                }
            }
        } else {
            doc.getElementById('rightPanel').innerHTML = '<div class="indemaCont" style="text-align: center"><p>Please try the TestProject clipper on another page. The images are too small to clip / display effectively.</p> <button @click="closeModal()">Continue Browsing</button></div>';
        }
    })
}

function getPageImages() {
    let images = doc.getElementsByTagName('img');
    let filteredImages = [];
    for (let i = 0; i < images.length; i++) {
        if (images[i].offsetParent != null && window.getComputedStyle(images[i]).display !== 'none' && images[i].clientHeight > 100 && images[i].clientWidth > 100 && images[i].alt !== 'Loading') {
            filteredImages.push(images[i]);
        }
    }
    return filteredImages;
}

function contentClick() {
    const sendUrl = pluginObject.url + 'set-data/' + pluginObject.api_key;
    pluginObject.pageData = doc.querySelectorAll("h1, h2, h3, h4, h5, h6, p, span, div, img");
    for (let i = 0; i < pluginObject.pageData.length; i++) {
        pluginObject.pageData[i].addEventListener('click', function (e) {
            if (pluginObject.activeState) {
                if (e.target.innerHTML !== '') {
                    if (e.target.nodeName === 'IMG') {
                        let clickedValue = e.target.src || e.target.dataset.src;
                        prepareDataToRequest(sendUrl, 'image', clickedValue);
                    } else {
                        let clickedValue = e.target.innerText;
                        prepareDataToRequest(sendUrl, 'text', clickedValue);

                    }
                    e.stopPropagation();
                } else {
                    if (e.target.nodeName === 'IMG') {
                        let clickedValue = e.target.src || e.target.dataset.src;
                        prepareDataToRequest(sendUrl, 'image', clickedValue);
                    } else {
                        let clickedValue = e.target.innerText;
                        prepareDataToRequest(sendUrl, 'text', clickedValue);
                    }
                    e.stopPropagation();
                }
            }

        });
    }
}

function prepareDataToRequest(sendUrl, type, clickedValue) {
    sendRequest('POST', sendUrl, {name: "image", value: clickedValue, type: 'text'}).then(res => {
        pluginObject.descriptionState = false;
        pluginObject.activeState = false;
        changeCursorStyle(false);
    }).catch(err => {
        console.log(err);
    });
}

(function () {
    var method;
    var noop = function () {
    };
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});
    while (length--) {
        method = methods[length];

        if (!console[method]) {
            console[method] = noop;
        }
    }
}());
window.addEventListener("message", (event) => {
    if (event.origin !== url)
        return;
    switch (event.data) {
        case 'active':
            pluginObject.activeState = true;
            changeCursorStyle(true);
            break;
        case 'deactive':
            pluginObject.activeState = false;
            changeCursorStyle(false);
            break;
        case 'reload':
            firstPageData();
            break;
        case 'close':
            document.getElementById('rightPanel').remove();
            location.reload()

    }
    // if (event.data === 'active') {
    //
    //
    // } else if (event.data === 'deactive') {
    //     pluginObject.activeState = false;
    //     changeCursorStyle(false);
    // } else if (event.data === 'reload') {
    //     firstPageData();
    // } else if (event.data === 'close') {
    //     document.getElementById('rightPanel').remove();
    // }
}, false);

function changeCursorStyle(state) {
    if (state) {
        doc.getElementsByTagName('body')[0].style = 'cursor: crosshair';
    } else {
        doc.getElementsByTagName('body')[0].style = 'cursor: unset';
    }
}

function getPageTitle() {
    let title = 'not found';
    if (doc.getElementsByTagName('h1').length > 0 && doc.getElementsByTagName('h1')[0].innerText) {
        title = doc.getElementsByTagName('h1')[0].innerText;
    } else {
        if (pluginObject.images[0].closest('p') && pluginObject.images[0].closest('p').innerText) {
            title = pluginObject.images[0].closest('p').innerText;
        } else if (pluginObject.images[0].closest('a') && pluginObject.images[0].closest('a').innerText) {
            title = pluginObject.images[0].closest('a').innerText;
        } else if (pluginObject.images[0].closest('span') && pluginObject.images[0].closest('span').innerText) {
            title = pluginObject.images[0].closest('span').innerText;
        } else if (pluginObject.images[0].closest('div') && pluginObject.images[0].closest('div').innerText) {
            title = pluginObject.images[0].closest('div').innerText;
        }
    }
    return title;

}
