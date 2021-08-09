<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Multiple File Upload with Progress Bar</title>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-2">
                <h1 class="mt-3 mb-3 text-center">Multiple File Upload with Progress Bar</h1>

                <!-- select files -->
                <div class="card">
                    <div class="card-header">Select File</div>
                    <div class="card-body text-center">
                        <input type="file" id="select_file" multiple />
                    </div>
                </div>
                <div class="display-images my-3" id="display-images">
                </div>

                <div class="row" hidden>
                    <button class="btn btn-success action-upload" id='action-upload'>
                        Upload files
                    </button>
                </div>
                <br />

                <!-- progress bar -->
                <div class="progress" id="progress_bar" style="display:none; ">
                    <div class="progress-bar" id="progress_bar_process" r ole="progressbar" style="width:0%">0%</div>
                </div>
                <div id="uploaded_image" class="row mt-5"></div>
            </div>
        </div>
    </div>
</body>

</html>

<!-- https://www.youtube.com/watch?v=MOpN365PEgU&list=PLbdAsYeJMKkZGzAgJ1A95ZYpIj3g4j9mi&index=3 -->

<script>
    'use strict';
    const $ = document.getElementById.bind(document);
    var inputFiles = $('select_file')
    const displayImages = $('display-images')

    function makeImageElement() {
        const img = document.createElement("img");
        img.style.width = '100px';
        img.style.height = '100px';
        img.style.objectFit = 'contain';
        img.style.boxShadow = '1px 1px 5px #999';
        img.style.marginRight = '5px';
        img.style.marginTop = '5px';
        return img
    }


    function displayImage(images) {
        //displayImages.innerText= '';
        for (var i = 0; i < images.length; i++) {
            const img = makeImageElement();
            img.src = images[i];
            displayImages.appendChild(img);
        }
    }

    /* display by select */
    function displaySelectImage(images) {
        displayImages.innerText = '';
        for (var image of Array.from(images)) {
            const img = makeImageElement();
            img.src = URL.createObjectURL(image);
            displayImages.appendChild(img);
        }
    }

    /* check select files and add to formData */

    function uploadFiles() {

        if (inputFiles.files.length < 1) {
            $('uploaded_image').innerHTML = '<div class="alert alert-danger">Selected File.</div>';
            setTimeout(() => {
                $('uploaded_image').innerHTML = ''
            }, 2000)
            return
        }

        const UPLOAD_TYPE = ['image/jpeg', 'image/png', 'video/mp4']
        var form_data = new FormData();
        var image_number = 1;
        var error = '';

        /* check select files and add to formData */
        for (var count = 0; count < inputFiles.files.length; count++) {
            var uploadFile = inputFiles.files[count]

            if (!UPLOAD_TYPE.includes(uploadFile.type)) {
                error += '<div class="alert alert-danger"><b>' + image_number + '</b> Selected File must be .jpg or .png Only.</div>';
            } else {
                form_data.append("images[]", uploadFile);
            }
            image_number++;
        }


        if (error) {
            $('uploaded_image').innerHTML = error;
            inputvarFiles.value = '';
        } else {

            $('progress_bar').style.display = 'block';
            var ajax_request = new XMLHttpRequest();
            ajax_request.open("POST", "upload.php");

            /* progress */
            ajax_request.upload.addEventListener('progress', function(event) {
                // console.log(1, event);
                var percent_completed = Math.round((event.loaded / event.total) * 100);
                $('progress_bar_process').style.width = percent_completed + '%';
                $('progress_bar_process').innerHTML = percent_completed + '% completed';

            });

            /* success  addEventListener('load', cb) oder onreadystatechange */
            /*  ajax_request.addEventListener('load', function(event) {
                 const imagesFile = JSON.parse(ajax_request.responseText)
                 displayImage(imagesFile)
                 showMessage()
             }); */

            ajax_request.onreadystatechange = function() {
                if (ajax_request.status == 200 && ajax_request.readyState == 4) {
                    const imagesFile = JSON.parse(ajax_request.responseText)
                    displayImage(imagesFile)
                    
                    $('uploaded_image').innerHTML = '<div class="alert alert-md alert-ms alert-success">Files Uploaded Successfully</div>';
                    inputFiles.value = '';
                    setTimeout(() => {
                        $('uploaded_image').innerHTML = '';
                        $('progress_bar').style.display = 'none';
                        $('progress_bar_process').style.width = 0;
                        $('progress_bar_process').innerHTML = '';
                    }, 2000)
                }
            }

            ajax_request.send(form_data);
        }
    }

    inputFiles.onchange = function() {
        uploadFiles() //  sofort upload
        //  displaySelectImage(inputFiles.files)
    };

    $('action-upload').addEventListener('click', uploadFiles)
</script>