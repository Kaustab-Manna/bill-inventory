<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0 text-white"><i class="fas fa-bell"></i> All Notifications & Alerts</h4>
        <a href="<?= base_url('notifications/mark-all-read') ?>" class="btn btn-sm btn-outline-light"><i class="fas fa-check-double"></i> Mark All as Read</a>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <?php if(empty($notifications)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-bell-slash fa-4x mb-3 text-secondary"></i>
                    <h4>No Notifications Found</h4>
                    <p>You're all caught up! No low stock alerts or important notices.</p>
                </div>
            <?php else: ?>
                <?php foreach($notifications as $n): ?>
                    <div class="list-group-item list-group-item-action p-4 border-bottom <?= $n->is_read ? 'bg-light' : '' ?>">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold <?= $n->type == 'low_stock' ? 'text-danger' : 'text-primary' ?>">
                                    <?php if($n->type == 'low_stock'): ?>
                                        <i class="fas fa-exclamation-circle text-danger me-2"></i>
                                    <?php else: ?>
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                    <?php endif; ?>
                                    <?= esc($n->title) ?>
                                    
                                    <?php if(!$n->is_read): ?>
                                        <span class="badge bg-danger ms-2">New</span>
                                    <?php endif; ?>
                                </h5>
                                <p class="mb-1 text-dark fs-6 mt-2"><?= esc($n->message) ?></p>
                                <small class="text-muted"><i class="far fa-clock"></i> <?= date('d M Y, h:i A', strtotime($n->created_at)) ?></small>
                            </div>
                            
                            <?php if(!$n->is_read): ?>
                                <div>
                                    <a href="<?= base_url('notifications/mark-read/'.$n->id) ?>" class="btn btn-sm btn-outline-secondary" title="Mark as Read"><i class="fas fa-check"></i></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
