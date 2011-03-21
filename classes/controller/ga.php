<?php
class Controller_Ga extends Controller{
    private $oAnalytics;
    private $conf;
    public function action_index(){
        $this->conf=Kohana::config("ga");

        $end_date = date("Y-m-d");
        $start_date = date("Y-m-d",
                strtotime ( '-30 day' , strtotime ( $end_date ) )
                ) ;


        $this->oAnalytics = new analytics($this->conf->ga_login, $this->conf->ga_password);
        $this->oAnalytics->setProfileById($this->conf->ga_profile);
        $this->oAnalytics->setDateRange($start_date, $end_date);
        $view=new View('ga');


        $view->start_date=$start_date;
        $view->end_date=$end_date;
        $view->topBrowsers= $this->graph($this->GA_query("getBrowsers"));
        $view->topReferrers = $this->graph($this->GA_query("getReferrers"));
        $view->topSearchWords = $this->graph($this->GA_query("getSearchWords"));
        $view->topScreenResolution = $this->graph($this->GA_query("getScreenResolution"));


        $visits = $this->GA_query("getVisitors");
        $views = $this->GA_query("getPageviews");

        foreach ($visits as $date => $visit)
        {
            $year = substr($date, 0, 4);
            $month = substr($date, 4, 2);
            $day = substr($date, 6, 2);

            $utc = mktime(date('h') + 1, NULL, NULL, $month, $day, $year);

            $flot_datas_visits[] = '[' . $utc . '000,' . $visit . ']';
            $flot_datas_views[] = '[' . $utc . '000,' . $views[$date] . ']';
        }

        $view->flot_data_visits = '[' . implode(',', $flot_datas_visits) . ']';
        $view->flot_data_views = '[' . implode(',', $flot_datas_views) . ']';

        $this->request->response=$view;

    }
    function graph($aData, $count=10){
        $iMax = max($aData);
        if ($iMax == 0){
            echo 'No data';
            return;
        }
        $sum=0;
        foreach($aData as $sKey => $sValue){
            $sum+=$sValue;
        }

        $st= '<table>';
        $i=0;
        foreach($aData as $sKey => $sValue){
            $st.= '  <tr>
                        <td>' . $sKey . '</td>
                        <td>' . $sValue . '</td>
                        <td>' . round($sValue/$sum*100,2) . '%</td>
                        <td><div class="bar" style="width: ' . intval(($sValue / $iMax) * 200) . 'px;"></div>
                    </tr>';
            $i++;
            if ($i>=$count) break;
        }
        $st.= '</table>';
        return $st;
    }

    function GA_query($ga_query) {
        if ( ! empty($this->conf->cache_lifetime))
        {
            $cache_key = "GA_query:$ga_query";
             if ($result = Kohana::cache($cache_key, NULL, $this->conf->cache_lifetime))
            {
                return $result;
            }
        }

		$result = $this->oAnalytics->$ga_query();

        if (isset($cache_key))
        {
            Kohana::cache($cache_key, $result, $this->conf->cache_lifetime);
        }

        return $result;
    }
}





?>








