<?php

use ceLTIc\LTI;
use ceLTIc\LTI\DataConnector;
use ceLTIc\LTI\Enum\ServiceAction;

/**
 * This page provides specific functions and classes for the application.
 *
 * @author  SZABO Zsolt <szazs@mm.bme.hu>
 * @copyright  BME-GPK Department of Applied Mechanics
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3
 */
require_once('db.php');
require_once('MyTool.php');


//??? $mypar = new MyPars;
//??? $page .= $mypar->myTask();
class MyPars
{
    const  MAXPOINT = 100;		// self::MAXPOINT
    public $L, $F, $a, $A, $B;		// parameters
    public $vA, $vB, $okA, $okB;	// user pars, and results of check
    public $pA= "p_A";			// public static?
    public $pB= "p_B";
    static $tol = 1e-3;			// tolerance value: self::$tol
    public $tic, $toc, $id, $uid;
    public $user;
    public $insert;			// true till first record

    public function __construct() {
        $L = rand(1,10);		// length of beam
        $F = rand(2,10);		// acting force
        $a = rand(2,7)/10*$L;		// distance of point of attack
        $A = $F*(1-$a/$L);		// calculated result for support A
        $B = $F-$A;			// calculated result for support B
        $this->L = $L;
        $this->F = $F;
        $this->a = $a;
        $this->A = $A;
        $this->B = $B;
        $this->tic = new DateTime();
        $this->toc = $this->tic->getTimestamp();	// for measuring ETA
        $this->user= strtoupper($_SESSION['ltiUsername']);
        $this->uid = $_SESSION['ltiUserId'];
        $this->id = substr($this->toc,-7,-1); // the last 6 digits of tstamp
        $this->insert = true;
    }

    public function check() {
        $this->vA = $this->readPost($this->pA);	// reading user supplied pars
        $this->vB = $this->readPost($this->pB);
        $this->okA= $this->chkVal($this->vA,$this->A,self::$tol);
        $this->okB= $this->chkVal($this->vB,$this->B,self::$tol);
        $this->toc = time() - $this->tic->getTimestamp();
        if( count($_POST) > 0 ) {
            $grade = $this->grade();
            if( is_numeric( $grade ) ) {
                $this->recordDb($this->id,$this->uid,$grade);
                $this->writeMoodle($grade);
            }
        }
    }

    private function readPost($key) {
        return isset($_POST[$key]) ? $_POST[$key] : "";
    }

###
### Compares user supplied $uVal to correct data $cDat with tolerance $tol
###
    private function chkVal($uVal,$cDat,$tol) {
        $OK = "<span style='color:green'>OK</span>";
        $XX = "<span style='color:red'>X</span>";
        $NN = "<span style='color:red'>N/Num</span>";
        if( is_numeric($uVal) ){
            return $uVal == abs(1-$uVal/$cDat) < $tol ? $OK : $XX;
        } else {
            return $uVal == "" ? "" : $NN;
        }
    }

###
### Grading according to the evaluations $okA and $okB
###
    public function grade() {
        $grade = 0;	// self::MAXPOINT;
        switch( preg_replace("/<[^>]+>(.).*/",'\1',$this->okA) ) {
            case "X": $grade += 10; break;	// -40 for false answer
            case "O": $grade += 50; break;	// -50 for no answer
            case "N": $grade -= 50; break;	// no answer or non numeric
        }
        switch( preg_replace("/<[^>]+>(.).*/",'\1',$this->okB) ) {
            case "X":  $grade += 10; break;
            case "O": $grade += 50; break;
            case "N": $grade -= 50; break;	// no answer or non numeric
        }
        return $grade>0 ? $grade : "";		// "" if no num. answer at all
    }

    private function writeMoodle($grade) {
        global $db;

        $dataConnector = DataConnector\DataConnector::getDataConnector($db, DB_TABLENAME_PREFIX);
        $rsrcLink = LTI\ResourceLink::fromRecordId($_SESSION['resource_pk'], $dataConnector);
        $uResLink = LTI\ResourceLink::fromRecordId($_SESSION['user_resource_pk'], $dataConnector);
        $userResult = LTI\UserResult::fromResourceLink($uResLink, $this->uid);

        $ltiOut = new LTI\Outcome($grade,self::MAXPOINT);
        $ok = $rsrcLink->doOutcomesService(ServiceAction::Write, $ltiOut, $userResult);
    }

    private function recordDb($itemPk,$userPk,$grade) {
        global $db;

        $prefix = DB_TABLENAME_PREFIX;
        $ok = true;
        if( $this->insert ) {
            $this->insert = false;	// go through this only once...
            $sql = <<< EOD
INSERT INTO {$prefix}mytool (item_pk, user_pk, username, grade,
    valA, valB, parF, parL, para, eta, start )
VALUES (:id, :uid, :user, :grade, :pA, :pB, :pF, :pL, :p_a, :eta, :start )
EOD;
            $query = $db->prepare($sql);
            $query->bindValue('id', $itemPk, PDO::PARAM_INT);
            $query->bindValue('uid', $userPk, PDO::PARAM_INT);
//            $query->bindValue('id', $this->id);
//            $query->bindValue('uid', $this->uid);
            $query->bindValue('user', $this->user );
            $query->bindValue('grade', $grade);
            $query->bindValue('pA', $this->A);
            $query->bindValue('pB', $this->B);
            $query->bindValue('pF', $this->F);
            $query->bindValue('pL', $this->L);
            $query->bindValue('p_a', $this->a);
            $query->bindValue('eta', $this->toc, PDO::PARAM_INT);
            $query->bindValue('start', $this->tic->format('Y-m-d H:i:s'), PDO::PARAM_STR );

            $ok = $query->execute();
        }// the values from $_POST are recorded via UPDATE below!!!

        $upd =  is_numeric($this->vA) ? 'ansA = :vA, ' : '';
        $upd .= is_numeric($this->vB) ? 'ansB = :vB, ' : '';
        if( $ok && $upd != "" ) { //&& is_numeric($grade) ...
            $sql = <<< EOD
UPDATE {$prefix}mytool
SET {$upd} grade = :grade, eta = :eta
WHERE (item_pk = :id)
EOD;
            $query = $db->prepare($sql);
            $query->bindValue('id', $this->id);
            if( $this->vA != "" ) $query->bindValue('vA', $this->vA);
            if( $this->vB != "" ) $query->bindValue('vB', $this->vB);
            $query->bindValue('grade', $grade);
            $query->bindValue('eta', $this->toc, PDO::PARAM_INT);

            $ok = $query->execute();
        }

        return $ok;
    }

}

?>
