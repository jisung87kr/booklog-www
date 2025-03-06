<?php
namespace App\Services\OpenAi;
use App\Models\Book;

class FunctionHandler{
    public function handle($functionName, $params=[])
    {
        switch ($functionName) {
            case 'get_weather':
                return $this->getWeather($params['location']);
            case 'getBooks':
                return $this->getBooks();
            case 'getBook':
                return $this->getBook($params['query']);
            default:
                throw new \Exception("Function not supported: " . $functionName);
        }
    }

    private function getWeather($location)
    {
        return ["location" => $location, "temperature" => "15°C"];
    }

    private function getBooks()
    {
        return Book::select('title', 'description', 'isbn')->inrandomorder()->limit(5)->get();
    }

    private function getBook($query)
    {
        return Book::select('title', 'description', 'isbn')->filter(['q' => $query])->first();
    }

}
