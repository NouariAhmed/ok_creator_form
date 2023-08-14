<?php
        // Function to get the book type name by its ID
        function getBookTypeName($conn, $bookTypeId) {
            $sql = "SELECT type_name FROM book_types WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $bookTypeId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $typeName);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            return $typeName;
        }

        // Function to get the book level name by its ID
        function getBookLevelName($conn, $bookLevelId) {
            $sql = "SELECT level_name FROM book_levels WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $bookLevelId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $levelName);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            return $levelName;
        }

        // Function to get the subject name by its ID
        function getSubjectName($conn, $subjectId) {
            $sql = "SELECT subject_name FROM subjects WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $subjectId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $subjectName);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            return $subjectName;
        }
        // Function to get student data by user ID
        function getStudentData($conn, $userId) {
            $sql = "SELECT studentLevel, studentSpecialty, baccalaureateRate, baccalaureateYear FROM student_data WHERE author_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $studentLevel, $studentSpecialty, $baccalaureateRate, $baccalaureateYear);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            return [
                'studentLevel' => $studentLevel,
                'studentSpecialty' => $studentSpecialty,
                'baccalaureateRate' => $baccalaureateRate,
                'baccalaureateYear' => $baccalaureateYear
            ];
            }
    
            // Function to get teacher data by user ID
            function getTeacherData($conn, $userId) {
            $sql = "SELECT teacherExperience, teacherCertificate, teacherRank, workFoundation FROM teacher_data WHERE author_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $teacherExperience, $teacherCertificate, $teacherRank, $workFoundation);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            return [
                'teacherExperience' => $teacherExperience,
                'teacherCertificate' => $teacherCertificate,
                'teacherRank' => $teacherRank,
                'workFoundation' => $workFoundation
            ];
            }
                    // Function to get inspector data by user ID
            function getInspectorData($conn, $userId) {
            $sql = "SELECT inspectorExperience, InspectorCertificate, inspectorRank, inspectorWorkFoundation FROM inspector_data WHERE author_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $inspectorExperience, $InspectorCertificate, $inspectorRank, $inspectorWorkFoundation);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            return [
                'inspectorExperience' => $inspectorExperience,
                'InspectorCertificate' => $InspectorCertificate,
                'inspectorRank' => $inspectorRank,
                'inspectorWorkFoundation' => $inspectorWorkFoundation
            ];
            }
            
            // Function to get doctor data by user ID
            function getDoctorData($conn, $userId) {
            $sql = "SELECT specialty, drWorkFoundation FROM doctor_data WHERE author_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $specialty, $drWorkFoundation);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            return [
                'specialty' => $specialty,
                'drWorkFoundation' => $drWorkFoundation
            ];
            }
            
            // Function to get trainer data by user ID
            function getTrainerData($conn, $userId) {
            $sql = "SELECT field, trainerExperience FROM trainer_data WHERE author_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $field, $trainerExperience);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            return [
                'field' => $field,
                'trainerExperience' => $trainerExperience
            ];
            }
            
            // Function to get novelist data by user ID
            function getNovelistData($conn, $userId) {
            $sql = "SELECT novelistfield FROM novelist_data WHERE author_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $novelistfield);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            return [
                'novelistfield' => $novelistfield
            ];
            }
