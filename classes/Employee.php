<?php
declare(strict_types=1);

namespace APP\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class Employee
{

    public function getEmployeeResult(int $offset = 0, $limit = 10)
    {
        $employee_arr = $this->getEmployeeArr();

        if (is_array($employee_arr) && !empty($employee_arr)) {
            $page_current = filter_var(($offset), FILTER_SANITIZE_NUMBER_INT);

            $offset                                         = (max($page_current - 1, 0)) * $limit;
            $result_count                        = count($employee_arr);
            $employee_arr_sliced   = array_slice($employee_arr, $offset, $limit);

            $html_result = '';

            //Build emloyee result list
            foreach ($employee_arr_sliced as $person)
            {

                $names              = $person['name'];
                $position           = $person['title'];
                $avatar              = $person['avatar'];
                $company       = $person['company'];
                $bio                       = $person['bio'];
                $bio                       = (strlen($bio) > 1) ? $bio : '';

               $html_result .= '  <article class="person_result">
              <div class="image">
                <img src="' . $avatar . '" alt="' . $names . '" width="56px" onerror="this.src=\'assets/image/no_image.png\'">
              </div>
              <div class="details">
                  <h2>' . $names . '</h2>
                  <h3 class="position">' . $position . '</h3>
                  <h3 class="company">' . $company . '</h3>
                  <p class="bio">' . $bio . '</p>
               </div>
            </article>';
            } //end foreach @employee_arr_sliced

            // Build pagination
            $total_results          = count($employee_arr);
            $total_pages            = intval(ceil($total_results / $limit));
            $page_current        = intval(($page_current == 0) ? 1 : $page_current);
            $page_next               = intval((($page_current * $limit) > $total_results) ? $total_pages : $page_current + 1);
            $page_prev               = intval(($page_current <= 0) ? 1 : $page_current - 1);
            $bef_prev                   = intval($page_prev - 1);
            $aft_next                    = intval($page_next + 1);

            if ($total_pages > 1)
            {
                $pagination = '<section class="pagination">';

                $pagination .= '<div class="btn_back ';
                if ($page_current != '1')
                {
                    $pagination .= ' "><li><a href="?offset=' . $page_prev . '">< Previous</a></li>';
                } else {
                    $pagination .= ' disabled"><li>< Previous</li>';
                }
                $pagination .= ' </div><ul>';

                if (($page_current >= 7))
                {
                    $pagination .= '<li><a href="?offset=0">1</a></li><li>...</li>
                                                        <li><a href="?offset=' . $bef_prev . '">' . $bef_prev . '</a></li>
                                                        <li><a href="?offset=' . $page_prev . '">' . $page_prev . '</a></li>
                                                        <li class="active">' . $page_current . '</li>';


                    if ($page_current < intval($total_pages - 1))
                    {
                        $pagination .= '<li><a href="?offset=' . $page_next . '">' . $page_next . '</a></li>';
                        if ($aft_next != $total_pages)
                        {
                            $pagination .= '<li><a href="?offset=' . $aft_next . '">' . $aft_next . '</a></li>';
                        }
                    }

                } else {
                    if ($total_pages > 10)
                    {
                        for ($i = 1; $i < 9; $i++)
                        {
                            $pagination .= ($i == $page_current) ? '<li class="active">' . $i . '</li>' : '<a href="?offset=' . $i . '"><li>' . $i . '</li></a>';
                        }
                    } else {
                        for ($i = 1; $i < $total_pages; $i++)
                        {
                            $pagination .= ($i == $page_current) ? '<li class="active">' . $i . '</li>' : '<a href="?offset=' . $i . '"><li>' . $i . '</li></a>';
                        }
                    }


                }

                if ($total_pages > 5 && $page_current < $total_pages && $page_current < ($total_pages - 1))
                {
                    $pagination .= '<li>...</li>';
                }


                if ($page_current != $total_pages)
                {
                    $pagination .= '<a href="?offset=' . $total_pages . '"><li>' . $total_pages . '</li></a>';
                }
                $pagination .= '</ul><div class="btn_next ';

                if ($page_current >= $total_pages)
                {
                    $pagination .= 'disabled"><li> Next > </li>';
                } else {
                    $pagination .= '"><a href="?offset=' . $page_next . '"><li> Next > </li></a>';
                }
                $pagination .= '</div>';

                if ($total_pages > 5)
                {
                    $pagination .= '</section>';
                }
            }
            $html_result .= $pagination;

            return json_encode(
                [
                'count' => $result_count,
                'list' => $html_result
                ], JSON_PRETTY_PRINT);
        }
    }

    private function getEmployeeArr()
    {

        require_once 'vendor/autoload.php';

        $headers = array(
            'auth'                          => [API_USER, API_PASS],
            'debug'                      => false,
            'headers'                 => [
                                                         'Accept'                  => 'application/json',
                                                         'Content-Type'  => 'application/json'
                                                        ]
            );

        $client = new Client();

        try {
            $response = $client->request('GET', API_LIST_URL, $headers);

            $result = $response->getBody()->getContents();

            $resultsArray = json_decode($result, true);

            // @$resultsArray sanitize result if array and output
            if (is_array($resultsArray))
            {
                $resultsArrayFiltered = $this->sanitizeResultArray($resultsArray);

                return $resultsArrayFiltered;
            }

        } catch (ClientException $e) {
//            print $e->getMessage();
        } catch (RequestException $e) {
//            print $e->getMessage();
        }
    }

    private function sanitizeResultArray(array $array): array
    {
        $array_sanitized = [];

        $var_filters = array(
            'uuid'                  => FILTER_SANITIZE_STRING,
            'company'     => FILTER_SANITIZE_STRING,
            'bio'                     => FILTER_SANITIZE_SPECIAL_CHARS,
            'name'              => FILTER_SANITIZE_STRING,
            'title'                  => FILTER_SANITIZE_STRING,
            'avatar'            => FILTER_SANITIZE_URL
        );

        foreach ($array as $value)
        {
            $value = preg_replace('~<script(.*?)</script>~Usi', "", $value);
            array_push($array_sanitized, filter_var_array(array_map('strip_tags', $value), $var_filters));
        }
        return $array_sanitized;
    }

}