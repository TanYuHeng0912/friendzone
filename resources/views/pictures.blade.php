@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="text-center upload mb-4">
            <form action="{{ route('pictures.add') }}" enctype="multipart/form-data" method="post" id="uploadForm">
                @csrf
                <input id="custom" type="file" name="picture[]" onchange="this.form.submit()" required="" multiple accept="image/*">
                <label class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Add Photos
                    <input
                        type="file"
                        name="picture[]"
                        onchange="this.form.submit()"
                        multiple
                        accept="image/*"
                        style="display: none;">
                </label>
            </form>
        </div>

        @if(count($pictures) == 0)
            <div class="text-center empty">
                <i class="fas fa-images empty-icon"></i>
                <h2>No photos yet</h2>
                <p>Add photos to show more of yourself!</p>
            </div>
        @else
            <div class="pictures-header mb-3">
                <h4>Your Photos <small class="text-muted">(Drag to reorder)</small></h4>
            </div>
            <div class="row" id="picturesContainer">
                @foreach($pictures as $picture)
                    <div class="col-md-4 col-lg-3 mb-4" data-picture-id="{{ $picture->id }}">
                        <div class="picture-card">
                            <div class="picture-wrapper">
                                <img src="{{ $picture->getPicture() }}" alt="User photo" class="picture-img">
                                <div class="picture-overlay">
                                    <button class="btn btn-sm btn-danger delete-picture-btn" 
                                            onclick="deletePicture({{ $picture->id }})"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <span class="picture-order-badge">{{ $loop->iteration }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-success" id="saveOrderBtn" onclick="saveOrder()" style="display: none;">
                    <i class="fas fa-save"></i> Save Order
                </button>
            </div>
        @endif
    </div>
@endsection

<style>
    .upload {
        padding: 20px;
    }

    .upload .btn {
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .upload .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .empty {
        padding: 60px 20px;
    }

    .empty-icon {
        font-size: 4rem;
        color: #ccc;
        margin-bottom: 20px;
    }

    .pictures-header h4 {
        color: #333;
        font-weight: 600;
    }

    .picture-card {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        cursor: move;
    }

    .picture-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .picture-card.dragging {
        opacity: 0.5;
    }

    .picture-wrapper {
        position: relative;
        width: 100%;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        overflow: hidden;
    }

    .picture-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .picture-card:hover .picture-img {
        transform: scale(1.05);
    }

    .picture-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, transparent 30%);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 10px;
    }

    .picture-card:hover .picture-overlay {
        opacity: 1;
    }

    .delete-picture-btn {
        background: rgba(220, 53, 69, 0.9);
        border: none;
        color: white;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .delete-picture-btn:hover {
        background: #dc3545;
        transform: scale(1.1);
    }

    .picture-order-badge {
        background: rgba(102, 126, 234, 0.9);
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    #picturesContainer.sortable-active {
        cursor: grabbing;
    }

    #picturesContainer .col-md-4.ui-sortable-helper {
        transform: rotate(5deg);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .dark-mode .picture-card {
        background: var(--card-bg);
    }

    .dark-mode .pictures-header h4 {
        color: var(--text-color);
    }
</style>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
<script>
let hasChanges = false;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sortable
    if (document.getElementById('picturesContainer')) {
        $("#picturesContainer").sortable({
            handle: '.picture-card',
            placeholder: "ui-state-highlight",
            tolerance: "pointer",
            start: function(event, ui) {
                ui.item.addClass('dragging');
                hasChanges = true;
                document.getElementById('saveOrderBtn').style.display = 'block';
            },
            stop: function(event, ui) {
                ui.item.removeClass('dragging');
            }
        });
    }
});

function saveOrder() {
    const pictureIds = [];
    document.querySelectorAll('[data-picture-id]').forEach(item => {
        pictureIds.push(parseInt(item.dataset.pictureId));
    });

    fetch('/pictures/reorder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ picture_ids: pictureIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update order badges
            document.querySelectorAll('.picture-order-badge').forEach((badge, index) => {
                badge.textContent = index + 1;
            });
            document.getElementById('saveOrderBtn').style.display = 'none';
            hasChanges = false;
            
            // Show success message
            const btn = document.getElementById('saveOrderBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Saved!';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save order');
    });
}

function deletePicture(id) {
    if (!confirm('Are you sure you want to delete this photo?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/pictures/${id}`;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    
    form.appendChild(csrf);
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
}

// Warn before leaving if there are unsaved changes
window.addEventListener('beforeunload', function(e) {
    if (hasChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
    
