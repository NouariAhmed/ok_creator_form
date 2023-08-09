<?php
// Custom code for fixed number of displayed buttons (6 buttons)
          $range = 2; // number of Btns dispalyed on page ! 
          $startPage = max(1, $currentPage - $range);
          $endPage = min($totalPages, $currentPage + $range);
          // Display "Previous" button with "disabled" style if on first page
          $prevPage = max(1, $currentPage - 1);
          echo '<a href="?page=' . $prevPage . '" class="btn btn-primary ' . ($currentPage === 1 ? 'disabled' : '') . '">Previous</a>';
          // Display page numbers with ellipsis
          if ($startPage > 1) {
              echo '<a href="?page=1" class="btn btn-primary">1</a>';
              if ($startPage > 2) {
                  echo '<span>...</span>';
              }
          }
          for ($i = $startPage; $i <= $endPage; $i++) {
              $activeClass = ($i === $currentPage) ? 'active' : '';
              echo '<a href="?page=' . $i . '" class="btn btn-primary ' . $activeClass . '">' . $i . '</a>';
          }
          if ($endPage < $totalPages) {
              if ($endPage < $totalPages - 1) {
                  echo '<span>...</span>';
              }
              echo '<a href="?page=' . $totalPages . '" class="btn btn-primary">' . $totalPages . '</a>';
          }
          // Display "Next" button with "disabled" style if on last page
          $nextPage = min($totalPages, $currentPage + 1);
          echo '<a href="?page=' . $nextPage . '" class="btn btn-primary ' . ($currentPage == $totalPages || $totalPages === 1 ? 'disabled' : '') . '">Next</a>';
          ?>