<?php

namespace App\Traits;

trait customPagination
{
    public function paginateFormat($data)
    {
        return [
            'datas' => $data->items(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'next_page_url' => $data->nextPageUrl(),
            'prev_page_url' => $data->previousPageUrl(),
            'per_page' => $data->perPage(),
            'total' => $data->total()
        ];
    }
}
