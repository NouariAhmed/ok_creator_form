<?php
session_start();
include('secure.php');
include('../connect.php');
include('../dash_functions.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('header.php');
    $searchQuery = trim($_POST["search_query"]);

    // Perform the database search using a parameterized query
    $sql = "SELECT * FROM authors WHERE authorfullname LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    $searchQueryWithWildcard = "%" . mysqli_real_escape_string($conn, $searchQuery) . "%";
    mysqli_stmt_bind_param($stmt, "s", $searchQueryWithWildcard);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $numResults = mysqli_num_rows($result);

    echo "<p class='text-center'>$numResults نتيجة : " . htmlspecialchars($searchQuery) . "</p>";
?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize pe-3">نتائج البحث</h6>
                    </div>
                </div>
                  <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 table-hover">
                            <thead>
                            <tr>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 ">المعرف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">المؤلف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">عنوان الكتاب</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">النوع والمستوى</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">المادة</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">العنوان</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">من طرف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">ملاحظات</th>
                      <th class="text-center text-secondary text-lg font-weight-bolder opacity-7">الإجراءات</th>
                           </tr>
                            </thead>
                            <tbody>
<?php
    // Display the search results
    if ($numResults > 0) {
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($items as $item) {
            ?>
           <tr>
                    <td class="align-middle text-sm">
                    <h6 class="mb-0 text-sm pe-3"><?php echo htmlspecialchars($item["communicate_date"]);?></h6>
                    <p class="text-xs text-secondary mb-0 pe-3"><?php echo htmlspecialchars($item["id"]);?>#</p>
                    <?php 
                     $author_status = htmlspecialchars($item["author_status"]);
                    // Use a switch statement to determine the appropriate badge
                    switch ($author_status) {
                      case "مقبول":
                          echo '<span class="badge badge-sm bg-gradient-success me-2 w-90">مقبول</span>';
                          break;
                      case "مرفوض":
                          echo '<span class="badge badge-sm bg-gradient-danger me-2 w-90">مرفوض</span>';
                          break;
                      case "قيد الدراسة":
                          echo '<span class="badge badge-sm bg-gradient-info me-2 w-90">قيد الدراسة</span>';
                          break;
                      case "مؤجل":
                          echo '<span class="badge badge-sm bg-gradient-warning me-2 w-90">مؤجل</span>';
                          break;
                      case "في الانتظار":
                          echo '<span class="badge badge-sm bg-gradient-secondary me-2 w-90">في الانتظار</span>';
                          break;
                      default:
                          echo '<span class="badge badge-sm bg-gradient-secondary me-2 w-90">Unknown</span>';
                          break;
                  }
                    ?>
                      </td>
                      <td>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($item["authorfullname"]);?></h6>
                            <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($item["phone"]);?></p>
                            <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($item["second_phone"]);?></p>
                            <p class="text-xs text-primary mb-0"><?php echo htmlspecialchars($item["author_type"]);?></p>
                          </div>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm">
                        <?php
                        $bookTitle = htmlspecialchars($item["book_title"]);
                        $titelWords = explode(' ', $bookTitle);
                        
                        $titelWordGroups = array_chunk($titelWords, 4);
                        foreach ($titelWordGroups as $titleGroup) {
                            echo implode(' ', $titleGroup) . "<br>";
                        }
                        ?>
                      </h6>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo getBookLevelName($conn, $item['book_level_id']); ?></h6>
                      <p class="text-xs text-secondary mb-0"><?php echo getBookTypeName($conn, $item['book_type_id']); ?></p>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo getSubjectName($conn, $item['subject_id']); ?></h6>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php
                        $address = htmlspecialchars($item["authorAddress"]);
                        $words = explode(' ', $address);
                        
                        $wordGroups = array_chunk($words, 4);
                        foreach ($wordGroups as $group) {
                            echo implode(' ', $group) . "<br>";
                        }
                        ?></h6>
                      <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($item["email"]);?></p>
                      <div class="ms-auto">
                          <?php if (!empty($item["fbLink"])) { ?>
                            <a href="<?php echo htmlspecialchars($item["fbLink"]); ?>" target="_blank">
                              <i class="fab fa-facebook"></i>
                            </a>
                          <?php } ?>
                          <?php if (!empty($item["instaLink"])) { ?>
                            <a href="<?php echo htmlspecialchars($item["instaLink"]); ?>" target="_blank">
                              <i class="fab fa-instagram"></i>
                            </a>
                          <?php } ?>
                          <?php if (!empty($item["youtubeLink"])) { ?>
                            <a href="<?php echo htmlspecialchars($item["youtubeLink"]); ?>" target="_blank">
                              <i class="fab fa-youtube"></i>
                            </a>
                          <?php } ?>
                          <?php if (!empty($item["tiktokLink"])) { ?>
                            <a href="<?php echo htmlspecialchars($item["tiktokLink"]); ?>" target="_blank">
                              <i class="fab fa-tiktok"></i>
                            </a>
                          <?php } ?>
                        </div>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($item["inserted_by_username"]);?></h6>
                      <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($item["created_at"]);?></p>
                      </td>
                      <td class="align-middle text-sm">
                        <h6 class="mb-0 text-sm">
                            <?php if (!empty($item['notes'])): ?>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="populateModal('<?php echo $item['notes']; ?>')">
                                    <i class="fas fa-comment-alt align-middle" style="font-size: 18px;"></i>
                                </button>
                            <?php endif; ?>
                        </h6>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">ملاحظات خاصة بالمؤلف: <?php echo $item['authorfullname']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="modalContent"></div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-center">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">غلق</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </td>
                        <td class="align-middle text-center">
                            <?php if (!empty($item["userfile"])): ?>
                                <a href="<?php echo htmlspecialchars($item["userfile"]); ?>" class="btn badge-sm bg-gradient-secondary" target="_blank">
                                    <i class="fas fa-file-pdf align-middle" style="font-size: 18px;"></i></a>
                            <?php endif; ?>
                            <a href="update_author.php?id=<?php echo htmlspecialchars($item["id"]);?>&type=<?php echo htmlspecialchars($item["author_type"]);?>&book_type_id=<?php echo htmlspecialchars($item["book_type_id"]); ?>" class="btn badge-sm bg-gradient-primary"> <i class="material-icons-round align-middle" style="font-size: 18px;">edit</i></a>
                            <a href="delete_author.php?id=<?php echo htmlspecialchars($item["id"]);?>&type=<?php echo htmlspecialchars($item["author_type"]);?>" class="btn badge-sm bg-gradient-danger"> <i class="material-icons-round align-middle" style="font-size: 18px;">delete</i></a>
                      </td>
                 
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 
 <?php
  mysqli_close($conn);
  include('footer.php');
                               
    } else {
        echo "<p class='text-center'>لم يتم إيجاد أي نتيجة.</p>";
    }
    
    mysqli_close($conn);
    include('footer.php');
    ob_end_flush();
    exit();
} else {
    // if the admin access direct to page redirect it 
    header("Location: index.php");
    exit();
}
?>
                       
