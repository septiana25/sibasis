<div class="col">
    <div class="card shadow-sm">
    <div class="card-body">
        <h5 id="title" class="mb-5">{{ $title }}</h5>
        <div id="note" class="d-flex justify-content-between align-items-center">
                <span class="badge bg-{{ $icons['bg'] }} d-inline-flex align-items-center justify-content-center rounded-circle p-3">
                    <i class="bi bi-{{ $icons['icon'] }} fs-5 text-{{ $icons['color'] }}"></i>
                </span>
            <h3>{{ $total }}</h3>

        </div>
    </div>
    </div>
</div>