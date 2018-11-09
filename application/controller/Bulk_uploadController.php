<?php

class Bulk_uploadController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handles what happens when user moves to URL/index/index - or - as this is the default controller, also
     * when user moves to /index or enter your application at base level
     */
    public function index()
    {
        $this->View->render('bulk_upload/index');
    }

    public function post_action () {

        $upload_successful = Bulk_uploadModel::csvFileUpload();;

        if ($upload_successful) {
            Redirect::to('bulk_upload/view');
        } else {
            Redirect::to('bulk_upload/index');
        }
    }

    public function view()
    {
        $this->View->render('bulk_upload/view', array(
                'uploads' => Bulk_uploadModel::getUploads())
        );
    }
}
