<?php
require('../functions.php');

$applicant_id = (int)$_GET['applicant_id'];

try {
    // Modified query to join with users table
    $notes = DB::query("
        SELECT an.*, u.user_name, u.picture 
        FROM applicant_notes an
        LEFT JOIN users u ON an.created_by = u.user_id
        WHERE an.applicant_id = %i 
        ORDER BY an.created_at DESC", 
        $applicant_id
    );
    
    if (count($notes) > 0) {
        echo '<div class="notes-container" style="max-height: 300px; overflow-y: auto; padding: 10px 20px;">';
        
        foreach ($notes as $note) {
            // User avatar handling
            $avatar = $note['picture'] ? 
                'background-image: url('.$note['picture'].');' : 
                'background-color: #FE5500; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;';
            
            $user_initials = !empty($note['picture']) ? '' : 
                substr($note['user_name'], 0, 1).substr($note['user_name'], 0, 1);
            
            echo '<div class="note-card p-2 border rounded-3 shadow-sm mb-2" id="note-' . $note['id'] . '" style="background-color: #fff;">';
            echo '  <div class="d-flex gap-2 align-items-start">';
            echo '    <div class="user-avatar" style="'.$avatar.'">'.$user_initials.'</div>';
            echo '    <div class="flex-grow-1">';
            echo '      <div class="d-flex justify-content-between align-items-center mb-2">';
            echo '        <div>';
            echo '          <span class="fw-semibold text-dark" style="font-size: 1rem;">'.$note['user_name'].'</span>';
            echo '          <small class="text-muted ms-2" style="font-size: 0.85rem;">'.formatDateTime($note['created_at']).'</small>';
            echo '        </div>';
            echo '        <div class="note-actions">';
            echo '          <a href="#" class="btn btn-sm text-white edit-note me-1" style="background-color: #FE5500;" 
                       data-note-id="' . $note['id'] . '" 
                       data-applicant-id="' . $applicant_id . '">
                       <i class="fas fa-edit"></i> Edit</a>';
            echo '          <a href="#" class="btn btn-sm text-white delete-note" style="background-color: #d9534f;" 
                       data-note-id="' . $note['id'] . '" 
                       data-applicant-id="' . $applicant_id . '">
                       <i class="fas fa-trash-alt"></i> Delete</a>';
            echo '        </div>';
            echo '      </div>';
            echo '      <p class="mb-2 note-text" style="font-size: 1rem; color: #555;">' . htmlspecialchars($note['note_text']) . '</p>';
            echo '    </div>';
            echo '  </div>';
            echo '</div>';
        }
        
        echo '</div>'; // End of notes-container
        
    } else {
        echo '<div class="text-center py-5 text-muted" id="noNotesMessage">';
        echo '  <i class="fas fa-sticky-note fa-3x mb-4"></i>';
        echo '  <p class="fs-4" style="color: #999;">No notes found for this applicant</p>';
        echo '</div>';
    }
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger m-3">Error loading notes: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// Helper function to format date
function formatDateTime($dateString) {
    $date = new DateTime($dateString);
    return $date->format('M j, Y \a\t g:i a');
}
?>
<style>
    .notes-container {
    scrollbar-width: thin;
    scrollbar-color: #FE5500 #f8f9fa;
    padding-right: 8px;
}

.notes-container::-webkit-scrollbar {
    width: 8px;
}

.notes-container::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 4px;
}

.notes-container::-webkit-scrollbar-thumb {
    background-color: #FE5500;
    border-radius: 4px;
    border: 2px solid #f8f9fa;
}

.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background-size: cover;
    background-position: center;
    flex-shrink: 0;
    color: white;
    font-size: 0.8rem;
}

.note-card {
    background-color: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 6px;
    transition: all 0.2s ease;
    position: relative;
}

.note-card:hover {
    background-color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.note-text {
    background-color: white;
    padding: 10px;
    border-radius: 8px;
    position: relative;
    margin-left: 6px;
}

.note-text::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 10px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 6px solid white;
}

.note-actions {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    position: absolute;
    top: 10px;
    right: 10px;
}

.note-card:hover .note-actions {
    opacity: 1;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .notes-container {
        padding: 10px;
    }
    
    .note-card {
        padding: 10px;
    }

    .user-avatar {
        width: 30px;
        height: 30px;
        font-size: 0.7rem;
    }

    .note-actions a {
        font-size: 0.8rem;
    }

    .note-text {
        font-size: 0.9rem;
        padding: 8px;
    }

    .note-card .d-flex {
        flex-direction: column;
    }

    .note-card .note-actions {
        margin-top: 10px;
    }
}

@media (max-width: 576px) {
    .note-actions a {
        font-size: 0.7rem;
        padding: 4px 8px;
    }

    .note-text {
        font-size: 0.85rem;
    }
}

</style>