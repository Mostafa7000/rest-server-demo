<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// FOR SIMPLICITY WE WON'T IMPLEMENT DELETE OPERATIONS.

/*
 * TODO: Get all students list. ALREADY IMPLEMENTED IN THE DEMO SESSION.
 * URL: GET /students
 * Response:
     Status code: 200
     JSON body:
         {
           "data": [
              {
                "id": "student_id",
                "name": "student_name",
                "email": "student_email",
                "phone": "student_phone"
              },
              {
                "id": "student_id",
                "name": "student_name",
                "email": "student_email",
                "phone": "student_phone"
              }
           ]
        }
 */

Route::get('/students', function (Request $request) {
    $rawData = DB::select(DB::raw("select id, name, email, phone from students"));

    $responseData = [];

    foreach ($rawData as $rd) {
        $responseData[] = [
            'id' => $rd->id,
            'name' => $rd->name,
            'email' => $rd->email,
            'phone' => $rd->phone,
        ];
    }

    $statusCode = 200;

    return response()->json([
        'data' => $responseData
    ], $statusCode);
});


/*
    * TODO: Create new student.
    * URL: POST /students
    * Request Body:
        {
            "name": "student_name",
            "email": "student_email",
            "phone": "student_phone"
        }
    * Response:
        status_code: 200
        JSON body:
            {
                "data": {
                    "id": "student_id_from_database"
                }
            }
*/
Route::post('/students', function (Request $request) {
    $data = $request->input();
    //print_r($data);

    $id = DB::table('students')->insertGetId([
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
    ]);

    return response()->json([
        'data' => [
            'id' => $id,
        ]
    ], 200);
});

/*
    * TODO: Get student details by id
    * URL: GET /students/{id}
    * Response:
       * success:
            status_code: 200
            JSON body:
                {
                    "data": {
                        "id": "student_id",
                        "name": "student_name",
                        "email": "student_email",
                        "phone": "student_phone"
                    }
                }
       * not found:
            status_code: 404
            JSON body:
                {
                    "data": {}
                }
*/

Route::get('/students/{id}', function ($id) {

    $rows = DB::select('select * from students where id = ?', [$id]);

    if (count($rows) == 0) {
        return response()->json([
            'data' => [],
        ], 404);
    }

    $student = $rows[0];
    //print_r($student);

    return response()->json([
        'data' => [
            "id" => $student->id,
            "name" => $student->name,
            "email" => $student->email,
            "phone" => $student->phone,
        ]
    ], 200);
});

/*
    * TODO: Update student data
    * URL: PUT /students/{id}
    * Request Body:
        {
            "name": "new_student_name",
            "email": "new_student_email",
            "phone": "new_student_phone"
        }
    * Response:
        status_code: 200
        JSON body:
            {
                "data": {
                    "id": "student_id",
                    "name": "new_student_name",
                    "email": "new_student_email",
                    "phone": "new_student_phone"
                }
            }
 */

Route::put('/students/{id}', function (Request $request, $id) {
    $newStudent = $request->input();

    DB::update(
        'update students
                set name = :name, email= :email, phone= :phone
                where id = :id',
        ['name' => $newStudent['name'], 'email' => $newStudent['email'], 'phone' => $newStudent['phone'], 'id' => $id]
    );

    return response()->json([
        'data' => [
            'id' => $id,
            'name' => $newStudent['name'],
            'email' => $newStudent['email'],
            'phone' => $newStudent['phone'],
        ]
    ]);
});


/*
   * TODO: For Courses implement Get, Create & Update endpoints same as students
   * Get all URL: GET /courses
   * Get course details: GET /courses/{id}
   * Create new course: POST /courses{id}
   * Update course: PUT /courses/{id}
   * Note: For JSON keys in both the request and response, let's use the same database columns names.
 */

Route::get('/courses', function (Request $request) {
    $rawData = DB::select(DB::raw("select id, name from courses"));

    $responseData = [];

    foreach ($rawData as $rd) {
        array_push($responseData, [
            'id' => $rd->id,
            'name' => $rd->name,
        ]);
    }

    $statusCode = 200;

    return response()->json([
        'data' => $responseData
    ], $statusCode);
});

Route::post('/courses', function (Request $request) {
    $data = $request->input();
    //print_r($data);

    $id = DB::table('courses')->insertGetId([
        'name' => $data['name'],
    ]);

    return response()->json([
        'data' => [
            'id' => $id,
        ]
    ], 200);
});

Route::get('/courses/{id}', function ($id) {

    $rows = DB::select('select * from courses where id = ?', [$id]);

    if (count($rows) == 0) {
        return response()->json([
            'data' => [],
        ], 404);
    }

    $student = $rows[0];
    //print_r($student);

    return response()->json([
        'data' => [
            "id" => $student->id,
            "name" => $student->name,
        ]
    ], 200);
});

Route::put('/courses/{id}', function (Request $request, $id) {
    $newStudent = $request->input();

    DB::update(
        'update courses
                set name = :name
                where id = :id',
        ['name' => $newStudent['name'], 'id' => $id]
    );

    return response()->json([
        'data' => [
            'id' => $id,
            'name' => $newStudent['name'],
        ]
    ]);
});

/*
  * TODO: Get all grades endpoint
  * URL: GET /grades
  * Response:
        status_code: 200
        JSON body: {
            "data": [
                {
                    "student_id": "STUDENT ID"
                    "course_id": "COURSE ID",
                    "grade": "GRADE"
                },
                {
                    "student_id": "STUDENT ID"
                    "course_id": "COURSE ID",
                    "grade": "GRADE"
                }
            ]
        }
  */

Route::get('/grades', function () {
    $grades = DB::select('select * from grades');

    $result = [];

    foreach ($grades as $grade) {
        array_push($result, [
            "student_id" => $grade->student_id,
            "course_id" => $grade->course_id,
            "grade" => $grade->grade
        ]);
    }

    return response()->json([
        'data' => $result,
    ]);
});

/*
   * TODO: Get grades for specific student only.
   * URL: GET /students/{student_id}/grades
   * Response:
        status_code: 200
        JSON body: {
            "data": [
                {
                    "student_id": "STUDENT ID"
                    "course_id": "COURSE ID",
                    "grade": "GRADE"
                },
                {
                    "student_id": "STUDENT ID"
                    "course_id": "COURSE ID",
                    "grade": "GRADE"
                }
            ]
        }
  */

Route::get('/students/{student_id}/grades', function ($id) {
    $grades = DB::select('select * from grades where student_id=?', [$id]);

    $result = [];

    foreach ($grades as $grade) {
        array_push($result, [
            "student_id" => $grade->student_id,
            "course_id" => $grade->course_id,
            "grade" => $grade->grade
        ]);
    }

    return response()->json([
        'data' => $result,
    ]);
});

/*
   * TODO: Get specific grade for specific student only. Shall return one record only if exists.
   * URL: GET /students/{student_id}/grades/{grade_id}
   * Response:
        status_code: 200
        JSON body: {
            "data": {
                "student_id": "STUDENT ID"
                "course_id": "COURSE ID",
                "grade": "GRADE"
            }
        }
  */

Route::get('/students/{student_id}/grades/{grade_id}', function ($student_id, $grade_id) {
    $grades = DB::select('select * from grades where student_id=? and id=?', [$student_id, $grade_id]);

    $result = [
        'student_id' => $grades[0]->student_id,
        'course_id' => $grades[0]->course_id,
        'grade' => $grades[0]->grade
    ];

    return response()->json([
        'data' => $result,
    ]);
});
