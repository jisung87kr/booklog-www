<?php
namespace App\Services\OpenAi;

class FunctionRegistry
{
    protected static $functions = [
        "get_weather" => [
            "type" => "function",
            "function" => [
                "name" => "get_weather",
                "description" => "Get current temperature for a given location.",
                "parameters" => [
                    "type" => "object",
                    "properties" => [
                        "location" => [
                            "type" => "string",
                            "description" => "City and country e.g. Bogotá, Colombia"
                        ]
                    ],
                    "required" => ["location"],
                    "additionalProperties" => false
                ],
                "strict" => true
            ]
        ],
        "getBooks" => [
            "type" => "function",
            "function" => [
                "name" => "getBooks",
                "description" => "랜덤한 도서를 조회합니다.",
            ]
        ],
        "getBook" => [
            "type" => "function",
            "function" => [
                "name" => "getBook",
                "description" => "도서 리뷰를 작성합니다.",
                "parameters" => [
                    "type" => "object",
                    "properties" => [
                        "query" => [
                            "type" => "string",
                            "description" => "도서의 ISBN 또는 제목"
                        ],
                    ],
                    "required" => ["query"],
                    "additionalProperties" => false
                ],
            ]
        ]
    ];

    /**
     * 특정 함수만 가져오기
     */
    public static function getTools(array $functionNames)
    {
        return array_values(array_intersect_key(self::$functions, array_flip($functionNames)));
    }
}
