<?php
namespace Simcify\Controllers;

// 
// use PHPExcel;
// require 'libs/phpexcel/PHPExcel.php';
use PHPExcel;
use PHPExcel_IOFactory;
use Simcify\Database;
use Simcify\Landa;
use Simcify\Auth;

class Invoice{

    /**
     * Get invoice view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");

        $students = array();
        foreach ($invoices as $invoice) {
            $students[$invoice->student] = array(
                'fname' => $invoice->fname,
                'lname' => $invoice->lname,
            );
        }
        
        return view('invoices', compact("invoices","user","students"));
    }

   /**
     * Add Payment invoice
     * 
     * @return null
     */
    public function addpayment() {
        $user = Auth::user();
        $invoice = Database::table('invoices')->where('id',input('invoice'))->first();
        $student = Database::table('users')->where('id',$invoice->student)->first();
        $data = array(
          'invoice'=>$invoice->id,
          'student'=>$invoice->student,
          'school'=>$invoice->school,
          'branch'=>$invoice->branch,
          'method'=>input('method'),
          'amount'=>input('amount'),
          'created_at'=>date('Y-m-d h:i:s', strtotime(input("payday")))
        );
        Database::table('payments')->insert($data);
        $data = array(
          'amountpaid'=>input('amount') + $invoice->amountpaid
        );
        Database::table('invoices')->where('id',$invoice->id)->update($data);
        $notification = sch_translate("you_made_a_payment_of", [money(input('amount'))]);
        Landa::notify($notification, $invoice->student, "payment", "personal");
        $notification = sch_translate("a_payment_of_recieved_from", [money(input('amount')), $student->fname, $student->lname]);
        Landa::notify($notification, $user->id, "payment");

        return response()->json(responder("success", sch_translate("alright"), sch_translate("payment_successfully_added"), "reload()"));
    }
    
    /**
     * Delete payment
     * 
     * @return Json
     */
    public function deletepayment() {
        $user = Auth::user();
        $payment = Database::table("payments")->where("id", input("paymentid"))->first();
        $invoice = Database::table('invoices')->where('id',$payment->invoice)->first();
        Database::table("payments")->where("id", input("paymentid"))->delete();
        $data = array(
          'amountpaid'=> $invoice->amount - $payment->amount
        );
        Database::table('invoices')->where('id',$invoice->id)->update($data);
        $notification = sch_translate("a_payment_of_deleted_from", [money($payment->amount)]);
        Landa::notify($notification, $invoice->student, "delete", "personal");
        $notification = sch_translate("a_payment_of_deleted_by", [money($payment->amount), $user->fname, $user->lname]);
        
        Landa::notify($notification, $user->id, "delete");
        return response()->json(responder("success", sch_translate("payment_deleted"), sch_translate("payment_successfully_deleted"), "reload()"));
    }
    
    /**
     * View payments
     * 
     * @return Json
     */
    public function viewpayments() {
        $payments = Database::table("payments")->where("invoice", input("invoiceid"))->get();
        return view('extras/payments', compact("payments"));
      }
    
    /**
     * Update invoice
     * 
     * @return Json
     */
    public function update() {
        $data = array(
            "item" => escape(input("item")),
            "amount" => escape(input("amount"))
        );
        Database::table("invoices")->where("id", input("invoiceid"))->update($data);
        return response()->json(responder("success", sch_translate("alright"), sch_translate("invoice_successfully_updated"), "reload()"));
    }
    
    /**
     * Update invoice view
     * 
     * @return Json
     */
    public function updateview() {
        $invoice = Database::table("invoices")->where("id", input("invoiceid"))->first();
        return view('extras/updateinvoice', compact("invoice"));
    }
    
    /**
     * Delete invoice
     * 
     * @return Json
     */
    public function delete() {
        $user = Auth::user();
        $invoice = Database::table("invoices")->where("id", input("invoiceid"))->first();
        Database::table("invoices")->where("id", input("invoiceid"))->delete();
        $notification = sch_translate("invoice_of_deleted_from", [$invoice->reference, money($invoice->amount)]);
        Landa::notify($notification, $invoice->student, "delete", "personal");
        $notification = sch_translate("invoice_of_deleted_by", [$invoice->reference, money($invoice->amount), $user->fname, $user->lname]);
        Landa::notify($notification, $user->id, "delete");
        return response()->json(responder("success", sch_translate("invoice_deleted"), sch_translate("invoice_successfully_deleted"), "reload()"));
    }


     /**
     * Download Invoice file
     * 
     * @return integer
     */
    public function download($invoiceid) {
        $invoice = Database::table('invoices')->where('id',$invoiceid)->first();
        $mpdf = new \Mpdf\Mpdf([
                        'tempDir' => config("app.storage")."mpdf",
                        'margin_top' => 0,
                        'margin_left' => 0,
                        'margin_right' => 0,
                        'mirrorMargins' => true
                    ]);
        $mpdf->WriteHTML(self::preview($invoiceid, "#fff"));
        $mpdf->Output("Invoice #".$invoice->reference.".pdf", 'D');
    }

    public function exportToExcel() {

        include_once('assets/libs/phpexcel/PHPExcel.php');

        $r = $_GET;
        
        $user = Auth::user();

        if ($r['bill_number'] == 'all' && $r['learner'] == 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else if ($r['bill_number'] != 'all' && $r['learner'] == 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("invoices`.`reference", $r['bill_number'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else if ($r['bill_number'] == 'all' && $r['learner'] != 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("users`.`id", $r['learner'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("invoices`.`reference", $r['bill_number'])->where("users`.`id", $r['learner'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        }

        $workbook = new PHPExcel();
        // var_dump($workbook);
        $sheet = $workbook->getActiveSheet();

        $workbook->getProperties()->setCreator('CREATOR')
            ->setTitle('Summary')
            ->setSubject('WESM Summary')
            ->setDescription('WESM Summary')
            ->setKeywords('Summary');
        $sheet->setTitle('Summary');

        $sheet->getProtection()->setSheet(false)->setSort(true)
            ->setInsertRows(true)
            ->setInsertColumns(true)
            ->setFormatCells(true);
        $sheet->setShowGridlines(true);

        // Column Headers
        $sheet->setCellValue('A1','#');
        $sheet->setCellValue('B1',sch_translate('students'));
        $sheet->setCellValue('C1',sch_translate('email'));
        $sheet->setCellValue('D1',sch_translate('ref').' #');
        $sheet->setCellValue('E1',sch_translate('amount'));
        $sheet->setCellValue('F1',sch_translate('paid'));
        $sheet->setCellValue('G1',sch_translate('balance'));
        $sheet->setCellValue('H1',sch_translate('date'));
        
        $ctr = 2;
        $ctrVal = 1;
        // loop value
        foreach($invoices as $invoice){
            
            $sheet->setCellValue('A'.$ctr, $ctrVal);
            $sheet->setCellValue('B'.$ctr, $invoice->fname . " ". $invoice->lname);
            $sheet->setCellValue('C'.$ctr, $invoice->email);
            $sheet->setCellValue('D'.$ctr, $invoice->reference);
            $sheet->setCellValue('E'.$ctr, money($invoice->amount));
            $sheet->setCellValue('F'.$ctr, money($invoice->amountpaid));
            $sheet->setCellValue('G'.$ctr, money($invoice->amount - $invoice->amountpaid));
            $sheet->setCellValue('H'.$ctr, date('d F Y',strtotime($invoice->created_at)));

            $ctr++;
            $ctrVal++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/download");
        $fileName = sch_translate('Payments').".xlsx";
        header("Content-Disposition: attachment;filename=$fileName");
        header('Cache-Control: max-age=0');

        $nCols = 8; //set the number of columns
        foreach (range(0, $nCols) as $col) {
            $workbook->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);                
        }

        $objWriter = PHPExcel_IOFactory::createWriter($workbook,'Excel2007','HTML');

        $objWriter->save('php://output');
    }

    public function exportToCsv() {

        $r = $_GET;

        $user = Auth::user();

        if ($r['bill_number'] == 'all' && $r['learner'] == 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else if ($r['bill_number'] != 'all' && $r['learner'] == 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("invoices`.`reference", $r['bill_number'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else if ($r['bill_number'] == 'all' && $r['learner'] != 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("users`.`id", $r['learner'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("invoices`.`reference", $r['bill_number'])->where("users`.`id", $r['learner'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        }

        $delimiter = ",";
        $filename = sch_translate('Payments').".csv";
        
        //create a file pointer
        $f = fopen('php://memory', 'w');

        //this will encode euro sign
        fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
    
        //set column headers
        $fields = array(
                '#', 
                mb_convert_encoding(sch_translate('students'), 'UTF-16LE', 'UTF-8'),
                mb_convert_encoding(sch_translate('email'), 'UTF-16LE', 'UTF-8'),
                mb_convert_encoding(sch_translate('ref').' #', 'UTF-16LE', 'UTF-8'),
                mb_convert_encoding(sch_translate('amount'), 'UTF-16LE', 'UTF-8'),
                mb_convert_encoding(sch_translate('paid'), 'UTF-16LE', 'UTF-8'),
                mb_convert_encoding(sch_translate('balance'), 'UTF-16LE', 'UTF-8'),
                mb_convert_encoding(sch_translate('date'), 'UTF-16LE', 'UTF-8')
            );

        fputcsv($f, $fields, $delimiter);

        $ctrVal = 1;
        // loop value
        foreach($invoices as $invoice){
            
            $lineData = array(
                $ctrVal, 
                $invoice->fname . " ". $invoice->lname, 
                $invoice->email, $invoice->reference,
                money($invoice->amount), 
                money($invoice->amountpaid), 
                money($invoice->amount - $invoice->amountpaid), 
                date('d F Y',strtotime($invoice->created_at))
            );

            fputcsv($f, $lineData, $delimiter);
            $ctrVal++;
        }
        
        //move back to beginning of file
        fseek($f, 0);
        
        //set headers to download file rather than displayed

        header('Content-Encoding: UTF-8');
        header("Content-type: text/csv; charset=UTF-8");
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        header("Pragma: no-cache");
        header("Expires: 0");
        
        //output all remaining data on a file pointer
        fpassthru($f);
    }

    public function exportToPdf() {

        $r = $_GET;

        $filename = sch_translate('Payments').".pdf";

        $mpdf = new \Mpdf\Mpdf([
                        'tempDir' => config("app.storage")."mpdf",
                        'margin_top' => 0,
                        'margin_left' => 0,
                        'margin_right' => 0,
                        'mirrorMargins' => true
                    ]);
        $mpdf->WriteHTML(self::paymentPdfPreview($r, "#F8F8F8"));
        $mpdf->Output($filename, 'D');
    }

    public function paymentPdfPreview($r = null, $background = "#F8F8F8") {

        if(!$r){
            $r['bill_number'] = $r['learner'] = 'all';
        }
        
        $user = Auth::user();

        $ctr = 1;

        $ctrTblSlice = 1;

        $page = 1;

        if ($r['bill_number'] == 'all' && $r['learner'] == 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else if ($r['bill_number'] != 'all' && $r['learner'] == 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("invoices`.`reference", $r['bill_number'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else if ($r['bill_number'] == 'all' && $r['learner'] != 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("users`.`id", $r['learner'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("invoices`.`reference", $r['bill_number'])->where("users`.`id", $r['learner'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        }

        return view('invoicepdfpreview', compact("invoices", "ctr", "background", "ctrTblSlice", "page"));

    }

    /**
     * Get invoice view
     * 
     * @return \Pecee\Http\Response
     */
    public function preview($invoiceid, $background = "#F8F8F8") {

        $invoice = Database::table('invoices')->where('id',$invoiceid)->first();
        $student = Database::table('users')->where('id',$invoice->student)->first();
        $school = Database::table('schools')->where('id',$invoice->school)->first();
        return view('invoicepreview', compact("invoice", "student", "school", "background"));

    }

    /**
     * Search invoice
     * 
     * @return Json
     */
    public function searchInvoice() {
        $r = $_REQUEST;

        $user = Auth::user();

        if (!isset($r['bill_number']) && !isset($r['learner'])) {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");

            $return = array();
            $count = 1;
            foreach ($invoices as $invoice) {
                if ( !empty($invoice->avatar) ) {
                    $student = '<img src="'.url('').'uploads/avatar/'.$invoice->avatar.'" class="table-avatar communication-avatar"><a href="'.url('Profile@get',['userid'=>$invoice->student]).'" class="text-primary"><strong>'.$invoice->fname.' '.$invoice->lname.'</strong></a><br><span class="communication-contact">'.$invoice->email.'</span>';
                } else {
                    $student = '<img src="'.url('').'assets/images/avatar.png" class="table-avatar communication-avatar"><a href="'.url('Profile@get',['userid'=>$invoice->student]).'" class="text-primary"><strong>'.$invoice->fname.' '.$invoice->lname.'</strong></a><br><span class="communication-contact">'.$invoice->email.'</span>';
                }

                $action = '<a class="btn btn-primary btn-sm btn-icon" target="_blank" href="'.url('Invoice@preview',['invoiceid'=>$invoice->id]).'"><i class="mdi mdi-eye"></i>'.sch_translate('Preview').'</a>
                        <div class="dropdown inline-block">
                            <button class="btn btn-default btn-sm btn-icofn dropdown-toggle" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                                <li role="presentation"><a role="menuitem" href=""  input="invoice" modal="#addpayment" class="pass-data" value="'.$invoice->id.'" > <i class="mdi mdi-credit-card-plus"></i>'.sch_translate('add_payment').'</a></li>
                                <li role="presentation"><a role="menuitem" href="" class="fetch-display-click" data="invoiceid:'.$invoice->id.'|csrf-token:'.csrf_token().'" url="'.url('Invoice@viewpayments').'" holder=".update-holder" modal="#update"> <i class="mdi mdi-credit-card"></i> '.sch_translate('Payments').'</a></li>
                                <li role="presentation"><a role="menuitem" href="'.url('Invoice@download',['invoiceid'=>$invoice->id]).'"> <i class="mdi mdi-cloud-download"></i> '.sch_translate('Download').'</a></li>
                                <li role="presentation"><a role="menuitem" href="" class="fetch-display-click" data="invoiceid:'.$invoice->id.'|csrf-token:'.csrf_token().'" url="'.url('Invoice@updateview').'" holder=".update-holder" modal="#update"> <i class="mdi mdi-pencil"></i> '.sch_translate('edit').'</a></li>
                                <li role="presentation"><a role="menuitem" href="" data="invoiceid:'.$invoice->id .'|csrf-token:'.csrf_token().'" url="'.url('Invoice@delete').'" warning-title="'.sch_translate('are_you_sure').'" warning-message="'.sch_translate('this_invoice_deleted').'" warning-button="'.sch_translate('delete').'" data-cancel-button="'.sch_translate('cancel').'" class="send-to-server-click"> <i class="mdi mdi-delete"></i> '.sch_translate('delete').'</a></li>
                            </ul>
                        </div>';


                $return['data'][] = array(
                    'id'        => $count,
                    'student'   => $student,
                    'ref'       => '#'.$invoice->reference,
                    'amount'    => money($invoice->amount),
                    'paid'      => money($invoice->amountpaid),
                    'balance'   => money($invoice->amount - $invoice->amountpaid),
                    'date'      => date('d F Y',strtotime($invoice->created_at)),
                    'action'    => $action,
                );
                $count++;
            }
        
            return response()->json($return);
        }

        if ($r['bill_number'] == 'all' && $r['learner'] == 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else if ($r['bill_number'] != 'all' && $r['learner'] == 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("invoices`.`reference", $r['bill_number'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else if ($r['bill_number'] == 'all' && $r['learner'] != 'all') {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("users`.`id", $r['learner'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        } else {
            $invoices = Database::table('invoices')->leftJoin('users','invoices.student','users.id')->where("invoices`.`branch", $user->branch)->where("invoices`.`reference", $r['bill_number'])->where("users`.`id", $r['learner'])->orderBy('invoices.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`users.email`","`invoices.id`", "`invoices.created_at`", "`invoices.amount`", "`invoices.amountpaid`", "`invoices.student`", "`invoices.reference`");
        }

        $return = array();
        $count = 1;
        foreach ($invoices as $invoice) {
            if ( !empty($invoice->avatar) ) {
                $student = '<img src="'.url('').'uploads/avatar/'.$invoice->avatar.'" class="table-avatar communication-avatar"><a href="'.url('Profile@get',['userid'=>$invoice->student]).'" class="text-primary"><strong>'.$invoice->fname.' '.$invoice->lname.'</strong></a><br><span class="communication-contact">'.$invoice->email.'</span>';
            } else {
                $student = '<img src="'.url('').'assets/images/avatar.png" class="table-avatar communication-avatar"><a href="'.url('Profile@get',['userid'=>$invoice->student]).'" class="text-primary"><strong>'.$invoice->fname.' '.$invoice->lname.'</strong></a><br><span class="communication-contact">'.$invoice->email.'</span>';
            }

            $action = '<a class="btn btn-primary btn-sm btn-icon" target="_blank" href="'.url('Invoice@preview',['invoiceid'=>$invoice->id]).'"><i class="mdi mdi-eye"></i>'.sch_translate('Preview').'</a>
                    <div class="dropdown inline-block">
                        <button class="btn btn-default btn-sm btn-icofn dropdown-toggle" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                                <li role="presentation"><a role="menuitem" href=""  input="invoice" modal="#addpayment" class="pass-data" value="'.$invoice->id.'" > <i class="mdi mdi-credit-card-plus"></i>'.sch_translate('add_payment').'</a></li>
                                <li role="presentation"><a role="menuitem" href="" class="fetch-display-click" data="invoiceid:'.$invoice->id.'|csrf-token:'.csrf_token().'" url="'.url('Invoice@viewpayments').'" holder=".update-holder" modal="#update"> <i class="mdi mdi-credit-card"></i> '.sch_translate('Payments').'</a></li>
                                <li role="presentation"><a role="menuitem" href="'.url('Invoice@download',['invoiceid'=>$invoice->id]).'"> <i class="mdi mdi-cloud-download"></i> '.sch_translate('Download').'</a></li>
                                <li role="presentation"><a role="menuitem" href="" class="fetch-display-click" data="invoiceid:'.$invoice->id.'|csrf-token:'.csrf_token().'" url="'.url('Invoice@updateview').'" holder=".update-holder" modal="#update"> <i class="mdi mdi-pencil"></i> '.sch_translate('edit').'</a></li>
                                <li role="presentation"><a role="menuitem" href="" data="invoiceid:'.$invoice->id .'|csrf-token:'.csrf_token().'" url="'.url('Invoice@delete').'" warning-title="'.sch_translate('are_you_sure').'" warning-message="'.sch_translate('this_invoice_deleted').'" warning-button="'.sch_translate('delete').'" data-cancel-button="'.sch_translate('cancel').'" class="send-to-server-click"> <i class="mdi mdi-delete"></i> '.sch_translate('delete').'</a></li>
                            </ul>
                    </div>';

            $return['data'][] = array(
                'id'        => $count,
                'student'   => $student,
                'ref'       => '#'.$invoice->reference,
                'amount'    => money($invoice->amount),
                'paid'      => money($invoice->amountpaid),
                'balance'   => money($invoice->amount - $invoice->amountpaid),
                'date'      => date('d F Y',strtotime($invoice->created_at)),
                'action'    => $action,
            );
            $count++;
        }

        return json_encode($return);
    }

}
