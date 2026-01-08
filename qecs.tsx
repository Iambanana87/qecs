import { Attachment } from '@/types';
import { IncidentType } from './IncidentTypes';
import { ComplaintType } from '@/constants/ComplaintTypes';

export interface ActionRow {
  id: number;
  action: string;
  responsible: string;
  status?: string;
}

export interface FiveMRow {
  id: number;
  code: string;
  cause: string;
  confirmed: boolean;
  description: string;
}

export interface CorrectiveActionRow {
  id: string;
  action: string;
  responsible: string;
  endDate: string;
  verification: string;
}

export interface EffectivenessRow {
  id: number;
  action: string;
  responsible: string;
  endDate: string;
  verification: string;
}

export interface PreventiveActionRow {
  id: string;
  action: string;
  responsible: string;
  endDate: string;
  verification: string;
}

export interface FlowElement {
  id: string;
  type?: string;
  data?: Record<string, unknown>;
  position?: { x: number; y: number };
  source?: string;
  target?: string;
  [key: string]: unknown;
}

export interface ValidationRow {
  id: number;
  item: string;
  standard: string;
  actual: string;
  result: string;
}

export interface WhyWhyRow {
  id: number;
  why: string;
  answer: string;
}
export interface MaterialCheckRow {
  id: number;
  machine: string;
  subAssembly: string;
  component: string;
  basicConditionDesc: string;
  currentCondition: string;
  beforePhoto: File | null;
  afterPhoto: File | null;
  responsible: string;
  control: string;
  status: 'OK' | 'NOK' | 'Pending' | '';
  closingDate: string;
}

export interface ParametersOperationRow {
  id: number;
  machine: string;
  subAssembly: string;
  component: string;
  standardSettingDesc: string;
  currentCondition: string;
  beforePhoto: File | null;
  afterPhoto: File | null;
  responsible: string;
  controlFrequency: string;
  status: 'OK' | 'NOK' | 'Pending' | '';
  closingDate: string;
}

export interface Complaint {
  // Section 1: General Info
  general: {
    subject: string;
    complaint_no: string;
    type: ComplaintType;
    customer: string;
    department: string;
    manager: string;
    lineArea: string;
    incidentType: IncidentType;
    productDescription: string;
    lotCode: string;
    productCode: string;
    machine: string;
    dateCode: string;
    problemOccurrence: string;
    problemDetection: string;
    reportTime: string;
    unitQtyAudited: number;
    unitQtyRejected: number;
    severityLevel: string;
    category: string;
    reportCompletedBy: string;
    detectionPoint: string;
    photos: Attachment[];
    partnerName: string;
    partnerCountry: string;
    partnerCode: string;
    partnerContact: string;
    partnerPhotos: Attachment[];
  };

  // Section 2: Floor / Spot Observation
  floor?: {
    observeLayers: FlowElement[];
    spotDetails: string;
  };

  // Section 4: Validation Tables
  materialMachineRows?: MaterialCheckRow[];
  parametersOperationRows?: ParametersOperationRow[];

  // Section 5: Problem Description (5W1H)
  problemDescription?: {
    what: string;
    where: string;
    when: string;
    who: string;
    which: string;
    how: string;
    phenomenonDescription: string;
    sketch?: Attachment;
  };

  // Section 6: Immediate Actions
  immediateActions?: ActionRow[];

  // Section 7: 5M Analysis
  fiveMAnalysis?: {
    manRows: FiveMRow[];
    materialRows: FiveMRow[];
    machineRows: FiveMRow[];
    methodRows: FiveMRow[];
    environmentRows: FiveMRow[];
  };

  // Section 8: Why-Why Analysis
  whyWhyAnalysis?: {
    happenRows: WhyWhyRow[];
    passRows: WhyWhyRow[];
  };

  // Section 9: Corrective Actions
  correctiveActions?: CorrectiveActionRow[];

  // Section 10: Effectiveness
  effectiveness?: {
    hasReoccurrence: 'yes' | 'no' | null;
    rows: EffectivenessRow[];
  };

  // Section 11: Preventive Actions
  preventiveActions?: {
    rows: PreventiveActionRow[];
    signatures: {
      complaintResponsible: string;
      productionRepresentative: string;
      qualityRepresentative: string;
      engineeringRepresentative: string;
      qualityManager: string;
    };
  };
}