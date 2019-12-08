CREATE OR REPLACE VIEW ml.measures_real_end_dates
AS SELECT m.measure_sid,
    m.goods_nomenclature_item_id,
    m.geographical_area_id,
    m.measure_type_id,
    m.measure_generating_regulation_id,
    m.ordernumber,
    m.reduction_indicator,
    m.additional_code_type_id,
    m.additional_code_id,
    m.measure_generating_regulation_role,
    m.justification_regulation_role,
    m.justification_regulation_id,
    m.stopped_flag,
    m.geographical_area_sid,
    m.goods_nomenclature_sid,
    m.additional_code_sid,
    m.export_refund_nomenclature_sid,
    to_char(m.validity_start_date, 'YYYY-MM-DD'::text) AS validity_start_date,
    LEAST(to_char(m.validity_end_date, 'YYYY-MM-DD'::text), to_char(r.validity_end_date, 'YYYY-MM-DD'::text), to_char(r.effective_end_date, 'YYYY-MM-DD'::text)) AS validity_end_date
   FROM measures m,
    base_regulations r
  WHERE m.measure_generating_regulation_id::text = r.base_regulation_id::text
UNION
 SELECT m.measure_sid,
    m.goods_nomenclature_item_id,
    m.geographical_area_id,
    m.measure_type_id,
    m.measure_generating_regulation_id,
    m.ordernumber,
    m.reduction_indicator,
    m.additional_code_type_id,
    m.additional_code_id,
    m.measure_generating_regulation_role,
    m.justification_regulation_role,
    m.justification_regulation_id,
    m.stopped_flag,
    m.geographical_area_sid,
    m.goods_nomenclature_sid,
    m.additional_code_sid,
    m.export_refund_nomenclature_sid,
    to_char(m.validity_start_date, 'YYYY-MM-DD'::text) AS validity_start_date,
    LEAST(to_char(m.validity_end_date, 'YYYY-MM-DD'::text), to_char(r.validity_end_date, 'YYYY-MM-DD'::text), to_char(r.effective_end_date, 'YYYY-MM-DD'::text)) AS validity_end_date
   FROM measures m,
    modification_regulations r
  WHERE m.measure_generating_regulation_id::text = r.modification_regulation_id::text;

-- Permissions

ALTER TABLE ml.measures_real_end_dates OWNER TO postgres;
GRANT ALL ON TABLE ml.measures_real_end_dates TO postgres;
