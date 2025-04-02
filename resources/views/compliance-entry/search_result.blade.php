<div class="col-lg-12 table-responsive">
    <table id="dataTable1" class="table table-striped table-bordered table-hover"
           style="margin-bottom: 20px; border-top: 3px solid #007bff">
        <thead>
        <tr style="background: #FFF; color: #337ab7; text-transform: uppercase; font-size: 12px">
            <th scope="col">Ticket ID</th>
            <th scope="col" style="width: 170px !important;">Update Date</th>
            <th scope="col">compliance No</th>
            <th scope="col">Compliance Level</th>
            <th scope="col">Category</th>
            <th scope="col">Sub Category</th>
            <th scope="col">Instruction type</th>
            <th scope="col">Applicable for</th>
            <th scope="col">Start Date</th>
            <th scope="col">Frequency</th>
            <th scope="col">Due Date</th>
            <th scope="col">Due Month</th>
            <th scope="col">Next Due Date</th>
            <th scope="col" style="font-size: 11px">Payment Penalty Implication Risk</th>
            <th scope="col">Compliance Owner</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($data as $info) {
        ?>
        <tr>
            <td>
                <a href="{{ url('compliance-entry-view/' . $info->id) }}" target="_blank">
                    <?php echo $info['ticket_id']?>
                </a>
            </td>
            <td><?php echo !empty($info->updated_at) ? date('d-M-Y h:i A', strtotime($info->updated_at)) : ''?></td>
            <td><?= $info['compliance_point_no']?></td>
            <td><?= $info['compliance_level']?></td>
            <td><?= $info['compliance_category']?></td>
            <td><?= $info['compliance_sub_category']?></td>
            <td><?= $info['instruction_type']?></td>
            <td><?= $info['compliance_applicable_for']?></td>
            <td><?= $info['start_date']?></td>
            <td><?= $info['frequency']?></td>
            <td><?= $info['due_date']?></td>
            <td><?= $info['due_month']?></td>
            <td><?= $info['next_due_date']?></td>
            <td><?= $info['payment_penalty_implication_risk']?></td>
            <td><?= $info['compliance_owner']?></td>
            <td><?= $info['status']?></td>
            <td>
                <a href="{{ url('compliance-entry-view/' . $info->id) }}"
                   class="btn btn-success btn-sm text-white mt-2">View</a>
            </td>

        </tr>
        <?php } ?>
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        <span style="float: left; margin-top: .5%"><b>Showing {{$data->perPage()}} records out of {{$data->total()}} total</b>&nbsp;&nbsp;&nbsp;</span>
        {{$data->links('pagination::bootstrap-4')}}
    </div>

</div>