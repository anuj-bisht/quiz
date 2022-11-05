<form method="POST" id="uploadCSV" enctype="multipart/form-data">
    <div class="col-md-3 margin-tb">                            
        <input type="file" class="form-control" name="file">
        <input type="hidden" name="module_name" id="file" value="{{$view_name}}">                                  
    </div>
    <div class="col-md-2 margin-tb">
        <button type="submit" id="uploadcsvbtn" class="btn btn-danger">
            Upload CSV
        </button>
        <img src="http://localhost:8081/quizs/public/img/spin.gif" style="max-width: 40px; display: none;" id="spinimg">
    </div>
</form>