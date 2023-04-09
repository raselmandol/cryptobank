<form method="post" class="ajax-screen-submit" autocomplete="off" action="<?php echo e(route('branches.store')); ?>" enctype="multipart/form-data">
	<?php echo e(csrf_field()); ?>


    <div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label"><?php echo e(_lang('Name')); ?></label>
				<input type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label"><?php echo e(_lang('Contact Email')); ?></label>
				<input type="text" class="form-control" name="contact_email" value="<?php echo e(old('contact_email')); ?>">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label"><?php echo e(_lang('Contact Phone')); ?></label>
				<input type="text" class="form-control" name="contact_phone" value="<?php echo e(old('contact_phone')); ?>">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label"><?php echo e(_lang('Address')); ?></label>
				<textarea class="form-control" name="address"><?php echo e(old('address')); ?></textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label"><?php echo e(_lang('Descriptions')); ?></label>
				<textarea class="form-control" name="descriptions"><?php echo e(old('descriptions')); ?></textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-lg"><i class="icofont-check-circled"></i> <?php echo e(_lang('Save')); ?></button>
			</div>
		</div>
	</div>
</form>
<?php /**PATH /home/k3lobc2fo9t4/blockchain/resources/views/backend/branch/modal/create.blade.php ENDPATH**/ ?>