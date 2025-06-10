<?php


/**
 *
 * Project status kanban view
 *
 */
function project_kanban_project_info( $status_id , $limit = 20 )
{

    $CI = &get_instance();

    if ( !empty( $CI->input->get('search') ) )
        $CI->db->where("c.company like '%".$CI->input->get('search')."%' ",null,false);

    if ( !empty( $CI->input->get('?search') ) )
        $CI->db->where("c.company like '%".$CI->input->get('?search')."%' ",null,false);

    if ( !has_permission('projects', '', 'view')  )
    {
        $CI->db->where('p.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')',null,false);
    }

    if ( !empty( $CI->input->get('filter_staff') ) )
    {

        $where = " p.id IN ( SELECT project_id FROM ".db_prefix()."project_members WHERE staff_id = ( ".$CI->input->get('filter_staff')." ) ) ";

        $CI->db->where($where,null,false);

    }


    if ( !empty( $CI->input->get('filter_client') ) )
    {
        $CI->db->where("p.clientid",$CI->input->get('filter_client'));
    }

    if ( !empty( $CI->input->get('from_date') ) )
    {

        $from_date = to_sql_date( $CI->input->get('from_date') );

        $where = " DATE(p.start_date) >= '$from_date' ";

        $CI->db->where($where,null,false);

    }

    if ( !empty( $CI->input->get('to_date') ) )
    {

        $to_date = to_sql_date( $CI->input->get('to_date') );

        $where = " DATE(p.start_date) <= '$to_date' ";

        $CI->db->where($where,null,false);

    }

    $sort = '';

    if ( !empty( $CI->input->get('sort_by') ) )
        $sort = $CI->input->get('sort_by');

    if ( !empty( $CI->input->get('?sort_by') ) )
        $sort = $CI->input->get('?sort_by');

    if (  !empty( $sort ) && !empty( $CI->input->get('sort') ) )
        $CI->db->order_by( $sort , $CI->input->get('sort') );



    $offset = 0;

    if ( !empty( $CI->input->get('page') ) )
    {

        $page = $CI->input->get('page');

        $offset = $page * $limit;

    }

    $projects = $CI->db->select('p.start_date, p.deadline, p.status , p.es_status_change_date , p.name as project_name, p.clientid , c.company, p.id as project_id ')
                        ->from(db_prefix().'projects p')
                        ->join(db_prefix().'clients c','c.userid = p.clientid')
                        ->where('p.status',$status_id)
                        ->limit($limit,$offset)
                        ->get()
                        ->result();


    return $projects;

}

function project_kanban_project_info_total( $status_id )
{

    $CI = &get_instance();

    if ( !empty( $CI->input->get('search') ) )
        $CI->db->where("c.company like '%".$CI->input->get('search')."%' ",null,false);

    if ( !has_permission('projects', '', 'view')  )
    {
        $CI->db->where('p.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')',null,false);
    }

    if ( !empty( $CI->input->get('filter_staff') ) )
    {

        $where = " p.id IN ( SELECT project_id FROM ".db_prefix()."project_members WHERE staff_id = ( ".$CI->input->get('filter_staff')." ) ) ";

        $CI->db->where($where,null,false);

    }

    if ( !empty( $CI->input->get('filter_client') ) )
    {
        $CI->db->where("p.clientid",$CI->input->get('filter_client'));
    }

    if ( !empty( $CI->input->get('from_date') ) )
    {

        $from_date = to_sql_date( $CI->input->get('from_date') );

        $where = " DATE(p.start_date) >= '$from_date' ";

        $CI->db->where($where,null,false);

    }

    if ( !empty( $CI->input->get('to_date') ) )
    {

        $to_date = to_sql_date( $CI->input->get('to_date') );

        $where = " DATE(p.start_date) <= '$to_date' ";

        $CI->db->where($where,null,false);

    }


    return $CI->db->from(db_prefix().'projects p')
                        ->join(db_prefix().'clients c','c.userid = p.clientid')
                        ->where('p.status',$status_id)
                        ->count_all_results();

}
