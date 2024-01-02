
// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('product-item-page');

if(sectionName){

    sectionName = parseInt(sectionName) + 1;
    //sectionName = parseInt(sectionName);
    var url = 'edit.php?post_type=product&page=menual-product-sync&product-item-page=' + sectionName;

    
    setTimeout(() => {
        window.location.replace(url);
    }, 2000);

}


// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('product-icpricp-page');

if(sectionName){

    //sectionName = parseInt(sectionName);
    sectionName = parseInt(sectionName) + 1;
    var url = 'edit.php?post_type=product&page=menual-product-sync&product-icpricp-page=' + sectionName;
    setTimeout(() => {
        window.location.replace(url);
    }, 2000);
}




// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('product-iciloc-page');

if(sectionName){

    sectionName = parseInt(sectionName) + 1;
    //sectionName = parseInt(sectionName);
    var url = 'edit.php?post_type=product&page=menual-product-sync&product-iciloc-page=' + sectionName;
    setTimeout(() => {
        window.location.replace(url);
    }, 2000);
}

// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('j3-mijoshop-product');

if(sectionName){

    sectionName = parseInt(sectionName) + 1;
    //sectionName = parseInt(sectionName);
    var url = 'edit.php?post_type=product&page=menual-product-sync&j3-mijoshop-product=' + sectionName;
    setTimeout(() => {
        window.location.replace(url);
    }, 2000);
}